define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Pagination, ChartStatic, Pageheader) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#teacher",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings : content.strings,
                        groups : content.groups,
                        userid : content.userid,
                        courseid : content.courseid,
                        timezone : content.timezone,
                        render_has : content.profile_render,

                        indicators: content.indicators,
                        search: null,
                        week_resources_categories: [],
                        week_resources_data: []

                    }
                },
                beforeMount(){
                    this.calculate_week_resources();
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#teacher").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        return '';
                    },

                    get_students_message(){
                        return (this.indicators.total_students == 1) ? this.strings.teacher_indicators_student : this.strings.teacher_indicators_students;
                    },

                    get_resources_message(){
                        return (this.indicators.total_cms == 1) ? this.strings.teacher_indicators_module : this.strings.teacher_indicators_modules;
                    },

                    get_weeks_message(){
                        return (this.indicators.total_weeks == 1) ? this.strings.teacher_indicators_week : this.strings.teacher_indicators_weeks;
                    },

                    calculate_week_resources() {
                        let categories = [], data = [];
                        let week_name;
                        this.indicators.weeks.forEach(week => {
                            week_name = `${week.name} ${(week.position+1)}`;
                            categories.push(week_name);
                            data.push(week.cms);
                        });
                        let name = this.capitalizeFirstLetter(this.strings.teacher_indicators_modules);
                        this.week_resources_categories = categories;
                        this.week_resources_data = [{ name, data}];
                    },

                    build_week_resources_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar'
                        };
                        chart.title = {
                            text: this.strings.teacher_indicators_week_resources_chart_title
                        };
                        chart.xAxis = {
                            categories: this.week_resources_categories
                        };
                        chart.yAxis = {
                            min: 0,
                                title: {
                                text: this.strings.teacher_indicators_week_resources_yaxis_title
                            }
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = this.week_resources_data;
                        return chart;
                    },

                    build_weeks_sessions_chart() {
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
                            categories: this.indicators.sessions.categories,
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
                                let days = vue.indicators.sessions.weeks[this.point.y][this.point.x] || '';
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = vue.strings.teacher_indicators_sessions;
                                if (this.point.value == 1) {
                                    label = vue.strings.teacher_indicators_session;
                                }
                                return '<b>' + yCategoryName + ' ' + xCategoryName + '</b>: '
                                    + this.point.value +' ' + label + '<br/>' + days;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.indicators.sessions.data,
                            dataLabels: {
                                enabled: false,
                            }
                        }];
                        return chart;
                    },

                    table_headers(){
                        let headers = [
                            { text: '', value : 'id', align : 'center', sortable : false},
                            { text: this.strings.thead_name , value : 'firstname'},
                            { text: this.strings.thead_lastname , value : 'lastname'},
                            { text: this.strings.thead_email , value : 'email'},
                            { text: this.strings.thead_progress , value : 'progress_percentage',  align : 'center'},
                            { text: this.strings.thead_sessions , value : 'sessions_number',  align : 'center'},
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
                        let module_label = this.strings.teacher_indicators_modules;
                        let finished_label = this.strings.teacher_indicators_finished;
                        if (item.cms.complete == 1) {
                            module_label = this.strings.teacher_indicators_module;
                            finished_label = this.strings.teacher_indicators_finalized;
                        }
                        return `${item.cms.complete} ${module_label} ${finished_label} ${this.strings.of_conector} ${item.cms.total}`;
                    },

                    get_point_category_name(point, dimension) {
                        let series = point.series,
                            isY = dimension === 'y',
                            axis = series[isY ? 'yAxis' : 'xAxis'];
                        return axis.categories[point[isY ? 'y' : 'x']];
                    },

                    capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
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