define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartcomponent",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, Chart, Pageheader) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', Chart);
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
                        hours_sessions: content.sessions_by_hours,
                        weeks_sessions: content.sessions_by_weeks,
                        progress_table: content.progress_table,
                        session_count: content.session_count,
                        search : null,
                    }
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#work_sessions").style.display = "block";
                },
                computed :{

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
                                this.hours_sessions = response.data.data.sessions_by_hours;
                                this.session_count = response.data.data.session_count;
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

                    build_chart_session_by_hours() {
                        let chart = new Object();
                        chart.title = {
                            text: this.strings.hours_sessions_title,
                        };
                        chart.chart = {
                            type: 'heatmap',
                            marginTop: 40,
                            marginBottom: 80,
                            plotBorderWidth: 0,
                            backgroundColor: '#FAFAFA',
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
                            margin: 30,
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = ' sesiones';
                                if (this.point.value == 1) {
                                    label = ' sesión';
                                }
                                return '<b>' + xCategoryName + ' ' + yCategoryName + '</b>: '
                                    + this.point.value +' ' + label;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.hours_sessions,
                            dataLabels: {
                                enabled: false,
                            }
                        }];
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    build_chart_session_by_weeks() {
                        let chart = new Object();
                        chart.title = {
                            text: this.strings.weeks_sessions_title,
                        };
                        chart.chart = {
                            type: 'heatmap',
                            marginTop: 40,
                            marginBottom: 80,
                            plotBorderWidth: 0,
                            backgroundColor: '#FAFAFA',
                        };
                        chart.xAxis = {
                            categories: this.strings.weeks,
                        };
                        chart.yAxis = {
                            categories: this.weeks_sessions.categories,
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
                            margin: 30,
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let days = vue.weeks_sessions.weeks[this.point.y][this.point.x] || '';
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = ' sesiones';
                                if (this.point.value == 1) {
                                    label = ' sesión';
                                }
                                return '<b>' + yCategoryName + ' ' + xCategoryName + '</b>: '
                                    + this.point.value +' ' + label + '<br/>' + days;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.weeks_sessions.data,
                            dataLabels: {
                                enabled: false,
                            }
                        }];
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    build_chart_session_count() {
                        let chart = new Object();
                        chart.chart = {
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.session_count_title,
                        };
                        chart.yAxis = {
                            title: {
                                text: this.strings.session_count_yaxis_title,
                            },
                            allowDecimals: false
                        };
                        chart.xAxis = {
                            categories: this.session_count.categories
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            margin: 10,
                            verticalAlign: 'bottom',
                        };
                        chart.series = this.session_count.data,
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    get_timezone(){
                        let information = `${this.strings.ss_change_timezone} ${this.timezone}`
                        return information;
                    },

                    table_headers(){
                        let headers = [
                            { text: '', value : 'id', align : 'center', sortable : false},
                            { text: this.strings.thead_name , value : 'firstname'},
                            { text: this.strings.thead_lastname , value : 'lastname'},
                            { text: this.strings.thead_email , value : 'email'},
                            { text: this.strings.thead_progress , value : 'progress_percentage',  align : 'center'},
                            { text: this.strings.thead_sessions , value : 'sessions',  align : 'center'},
                            { text: this.strings.thead_time , value : 'inverted_time', align : 'center'},
                        ];
                        return headers;
                    },

                    get_picture_url(userid){
                        let url = `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                        return url;
                    },

                    get_percentage_progress(value){
                        return `${value} %`;
                    },

                    get_progress_tooltip(item){
                        let module_label = this.strings.modules_label;
                        let finished_label = this.strings.finisheds_label;
                        if (item.complete_cms == 1) {
                            module_label = this.strings.module_label;
                            finished_label = this.strings.finished_label;
                        }
                        return `${item.complete_cms} ${module_label} ${finished_label} ${this.strings.of_conector} ${item.total_cms}`;
                    },
                }
            })
        }

        return {
            init : init
        };
    });