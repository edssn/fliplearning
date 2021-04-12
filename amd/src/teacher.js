define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Pagination, ChartStatic, PageHeader, HelpDialog) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', PageHeader);
            Vue.component('helpdialog', HelpDialog);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#teacher",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings: content.strings,
                        groups: content.groups,
                        userid: content.userid,
                        courseid: content.courseid,
                        timezone: content.timezone,
                        render_has: content.profile_render,

                        indicators: content.indicators,
                        weeks_sessions_colors: content.weeks_sessions_colors,
                        week_resources_colors: content.week_resources_colors,
                        search: null,
                        week_resources_categories: [],
                        week_resources_data: [],

                        help_dialog: false,
                        help_contents: [],
                    }
                },
                beforeMount() {
                    this.calculate_week_resources();
                },
                mounted() {
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#teacher").style.display = "block";
                },
                methods: {
                    get_help_content() {
                        let contents = [];
                        contents.push({
                            title: this.strings.section_help_title,
                            description: this.strings.section_help_description,
                        });
                        return contents;
                    },

                    get_course_grade() {
                        let grade = Number(this.indicators.course.grademax);
                        return (this.isInt(grade)) ? grade : grade.toFixed(2);
                    },

                    calculate_week_resources() {
                        let categories = [],
                            data = [];
                        let week_name;
                        this.indicators.weeks.forEach(week => {
                            week_name = `${week.name} ${(week.position+1)}`;
                            categories.push(week_name);
                            data.push(week.cms);
                        });
                        let name = this.capitalizeFirstLetter(this.strings.teacher_indicators_modules);
                        this.week_resources_categories = categories;
                        this.week_resources_data = [{ name, data }];
                    },

                    build_week_resources_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.week_resources_colors;
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
                        chart.chart = {
                            type: 'heatmap',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
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
                            minColor: this.weeks_sessions_colors[0],
                            maxColor: this.weeks_sessions_colors[1],
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            useHTML: true,
                            formatter: function() {
                                let days = vue.indicators.sessions.weeks[this.point.y][this.point.x] || '';
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                return '<small>' + yCategoryName + ' ' + xCategoryName + '</small><br/>' +
                                    '<b style="color: ' + vue.weeks_sessions_colors[1] + ';">• </b>' +
                                    vue.strings.sessions_text + ': ' + this.point.value + '<br/>' +
                                    '<small>' + days + '</small>';
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.indicators.sessions.data,
                        }];
                        return chart;
                    },

                    table_headers() {
                        let headers = [
                            { text: '', value: 'id', align: 'center', sortable: false },
                            { text: this.strings.thead_name, value: 'firstname' },
                            { text: this.strings.thead_lastname, value: 'lastname' },
                            { text: this.strings.thead_email, value: 'email' },
                            { text: this.strings.thead_progress, value: 'progress_percentage', align: 'center' },
                            { text: this.strings.thead_sessions, value: 'sessions_number', align: 'center' },
                            { text: this.strings.thead_time, value: 'inverted_time', align: 'center' },
                        ];
                        return headers;
                    },

                    get_picture_url(userid) {
                        let url = `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                        return url;
                    },

                    get_percentage_progress(value) {
                        return `${value} %`;
                    },

                    get_progress_tooltip(item) {
                        return this.strings.finished_resources + ': ' + item.cms.complete + ' ' +
                            this.strings.of_conector + ' ' + item.cms.total;
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

                    isInt(n) {
                        return n % 1 === 0;
                    },

                    open_chart_help(chart) {
                        let contents = [];
                        if (chart == "week_resources") {
                            contents.push({
                                title: this.strings.week_resources_help_title,
                                description: this.strings.week_resources_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.week_resources_help_description_p2,
                            });
                        } else if (chart == "weeks_sessions") {
                            contents.push({
                                title: this.strings.weeks_sessions_help_title,
                                description: this.strings.week_sessions_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.week_sessions_help_description_p2,
                            });
                        } else if (chart == "progress_table") {
                            contents.push({
                                title: this.strings.progress_table_help_title,
                                description: this.strings.progress_table_help_description,
                            });
                        }
                        this.help_contents = contents;
                        if (this.help_contents.length) {
                            this.help_dialog = true;
                        }
                    },

                    update_help_dialog(value) {
                        this.help_dialog = value;
                    },

                    get_timezone() {
                        let information = `${this.strings.change_timezone} ${this.timezone}`
                        return information;
                    },

                }
            })
        }

        return {
            init: init
        };
    });