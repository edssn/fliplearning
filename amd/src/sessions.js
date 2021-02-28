define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartStatic, Pageheader) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#work_sessions",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings : content.strings,
                        groups : content.groups,
                        userid : content.userid,
                        courseid : content.courseid,
                        timezone : content.timezone,
                        render_has : content.profile_render,
                        loading : false,
                        errors : [],
                        pages : content.pages,
                        hours_sessions: content.indicators.sessions,
                        session_count: content.indicators.count,
                        inverted_time: content.indicators.time,
                        search: null,
                    }
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#work_sessions").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        let helpcontents = [];
                        let time = (this.render_has == 'teacher')
                            ? this.strings.time_inside_plataform_description_teacher
                            : this.strings.time_inside_plataform_description_student;
                        let activity = (this.render_has == 'teacher')
                            ? this.strings.activity_inside_plataform_description_teacher
                            : this.strings.activity_inside_plataform_description_student;
                        helpcontents.push({
                            title: this.strings.time_inside_plataform,
                            description: time,
                        });
                        helpcontents.push({
                            title: this.strings.activity_inside_plataform,
                            description: activity,
                        });
                        return helpcontents;
                    },

                    update_interactions(week){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "worksessions",
                            userid : this.userid,
                            courseid : this.courseid,
                            weekcode : week.weekcode,
                            profile : this.render_has,
                        }
                        Axios({
                            method:'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params : data,
                        }).then((response) => {
                            if (response.status == 200 && response.data.ok) {
                                this.hours_sessions = response.data.data.indicators.sessions;
                                this.session_count = response.data.data.indicators.count;
                                this.inverted_time = response.data.data.indicators.time;
                            } else {
                                this.error_messages.push(this.strings.error_network);
                            }
                        }).catch((e) => {
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    get_point_category_name(point, dimension) {
                        let series = point.series,
                            isY = dimension === 'y',
                            axis = series[isY ? 'yAxis' : 'xAxis'];
                        return axis.categories[point[isY ? 'y' : 'x']];
                    },

                    build_hours_sessions_chart() {
                        let chart = new Object();
                        chart.title = {
                            text: null,
                        };
                        chart.chart = {
                            type: 'heatmap',
                            backgroundColor: null,
                        };
                        chart.xAxis = {
                            categories: this.strings.days,
                        };
                        chart.yAxis = {
                            categories: this.strings.hours,
                            title: null,
                            reversed: true,
                        };
                        chart.colorAxis = {
                            min: 0,
                            stops: [
                                [0.0, '#E0E0E0'],
                                [0.25, '#D6E7F9'],
                                [0.50, '#9AC4EF'],
                                [0.75, '#5DA1E5'],
                                [1, '#3384D6'],
                            ],
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = vue.strings.sessions_text;
                                if (this.point.value == 1) {
                                    label = vue.strings.session_text;
                                }
                                return '<b>' + xCategoryName + ' ' + yCategoryName + '</b>: '
                                    + this.point.value +' ' + label;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.hours_sessions,
                        }];
                        return chart;
                    },

                    build_inverted_time_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.xAxis = {
                            type: 'category',
                            crosshair: true,
                        };
                        chart.yAxis = {
                            title: {
                                text: this.strings.time_inverted_x_axis,
                            }
                        };
                        chart.tooltip = {
                            shared:true,
                            useHTML:true,
                            formatter: function () {
                                let category_name = this.points[0].key;
                                let time = vue.convert_time(this.y);
                                return `<b>${category_name}: </b>${time}`;
                            }
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = [{
                            colorByPoint: true,
                            data: this.inverted_time.data
                        }];
                        return chart;
                    },

                    build_chart_session_count() {
                        let chart = new Object();
                        chart.chart = {
                            backgroundColor: null,
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.yAxis = {
                            title: {
                                text: this.strings.session_count_yaxis_title,
                            },
                            allowDecimals: false
                        };
                        chart.xAxis = {
                            categories: this.session_count.categories,
                        };
                        chart.tooltip = {
                            valueSuffix: this.strings.session_count_tooltip_suffix,
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.series = this.session_count.data
                        return chart;
                    },

                    convert_time(time) {
                        time *= 3600; // pasar las horas a segundos
                        let h = this.strings.hours_short;
                        let m = this.strings.minutes_short;
                        let s = this.strings.seconds_short;
                        let hours = Math.floor(time / 3600);
                        let minutes = Math.floor((time % 3600) / 60);
                        let seconds = Math.floor(time % 60);
                        let text;
                        if (hours >= 1) {
                            if (minutes >= 1) {
                                text = `${hours}${h} ${minutes}${m}`;
                            } else {
                                text = `${hours}${h}`;
                            }
                        } else if ((minutes >= 1)) {
                            if (seconds >= 1) {
                                text = `${minutes}${m} ${seconds}${s}`;
                            } else {
                                text = `${minutes}${m}`;
                            }
                        } else {
                            text = `${seconds}${s}`;
                        }
                        return text;
                    },

                    info() {
                        console.log('Open modal');
                    },

                    get_timezone(){
                        let information = `${this.strings.ss_change_timezone} ${this.timezone}`
                        return information;
                    },
                }
            })
        }

        return {
            init : init
        };
    });