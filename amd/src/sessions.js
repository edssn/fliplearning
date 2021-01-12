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
            new Vue({
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
                        data: content.data,
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
                        let validresponse = false;
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
                            validresponse = true;
                            this.data = response.data.sessions;
                        }).catch((e) => {
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    build_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'heatmap',
                            marginTop: 40,
                            marginBottom: 80,
                            plotBorderWidth: 0,
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: null
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
                                    [0.2, '#DBDDE0'],
                                    [0.4, '#D6E7F9'],
                                    [0.6, '#9AC4EF'],
                                    [0.8, '#5C9FE2'],
                                    [1, '#327FCE'],
                            ],
                        };
                        chart.legend = {
                            layout: 'horizontal',
                                margin: 30,
                                verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let x = this.point.x;
                                let y = this.point.y;
                                let xCategoryName = this.point.series.xAxis.categories[x];
                                let yCategoryName = this.point.series.yAxis.categories[y];
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
                            data: this.data,
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
                        chart.responsive = {
                            rules: [{
                                condition: {
                                    maxWidth: 500
                                },
                                chartOptions: {
                                    yAxis: {
                                        labels: {
                                            formatter: function () {
                                                return this.value.charAt(0);
                                            }
                                        }
                                    }
                                }
                            }]
                        };
                        return chart;
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