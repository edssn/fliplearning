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
                el: "#grades",
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

                        grades: content.grades,
                        default_category: null,
                        average_categories: [],
                        average_data: [],
                        selected_items: [],
                        item_details: [],

                        grade_item_details_title: "",
                        grade_item_details_categories: [],
                        grade_item_details_data: [],
                        selected_item: null,
                    }
                },
                beforeMount(){
                    if (this.grades.categories.length) {
                        this.default_category = this.grades.categories[0];
                        this.calculate_chart_items_average(this.default_category.items);
                        let item = this.find_first_grade_item(this.default_category.items);
                        this.calculate_chart_item_grade_detail(item);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#grades").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    changeCategory(items) {
                        this.calculate_chart_items_average(items);
                        let item = this.find_first_grade_item(items);
                        this.calculate_chart_item_grade_detail(item);
                    },

                    build_chart_items_average() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.grades_chart_title
                        };
                        chart.xAxis = {
                            categories: this.average_categories
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            let position = this.x;
                                            let item = vue.selected_items[position];
                                            vue.calculate_chart_item_grade_detail(item);
                                        }
                                    }
                                }
                            }
                        };
                        chart.tooltip = {
                            formatter: function() {
                                let position = this.point.x;
                                let item = vue.selected_items[position];
                                let name = this.x;
                                let average = item.average.toFixed(2);
                                let grademax = item.grademax;
                                let count = item.gradecount;
                                let text = '<b>' + name + '<b> <br/>' +
                                    vue.strings.grades_tooltip_average + ': ' + average + '<br/>' +
                                    vue.strings.grades_tooltip_grade + ': ' + grademax + '<br/>' +
                                    count + ' ' + vue.strings.grades_tooltip_students + ' ' +
                                    vue.grades.student_count
                                    + '<br/>';
                                return text;
                            }
                        };
                        chart.yAxis = [{
                            allowDecimals: false,
                            labels: {
                                format: '{value} %',
                            },
                            title: {
                                text: this.strings.grades_yaxis_title,
                            }
                        }];
                        chart.series = [{
                            data: this.average_data,
                        }];
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    build_chart_item_grade_detail() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.grade_item_details_title,
                        };
                        chart.xAxis = {
                            categories: this.strings.grade_item_details_categories,
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            formatter: function() {
                                let category = this.x;
                                let name = vue.selected_item.itemname;
                                let maxgrade = vue.selected_item.grademax;
                                let grade = 0;
                                if (this.point.x == 0) {
                                    grade = vue.selected_item.maxrating;
                                } else if (this.point.x == 1) {
                                    grade = vue.selected_item.average;
                                } else {
                                    grade = vue.selected_item.minrating;
                                }

                                let text = '<b>' + name + '<b> <br/>' +
                                    category + ': ' + grade + '/' + maxgrade + '<br/>';
                                return text;
                            }
                        };
                        chart.yAxis = [{
                            title: {
                                enabled: false,
                            }
                        }];
                        chart.series = [{
                            data: this.grade_item_details_data,
                        }];
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    calculate_chart_items_average(items) {
                        let values = [];
                        let categories = [];
                        items.forEach(item => {
                            values.push(item.average_percentage);
                            categories.push(item.itemname);
                        });
                        this.average_categories = categories;
                        this.average_data = values;
                        this.selected_items = items;
                    },

                    calculate_chart_item_grade_detail(item) {
                        this.selected_item = item;
                        let item_data = [0, 0, 0];
                        if (item) {
                            item_data = [
                                Number(item.maxrating.toFixed(2)),
                                Number(item.average.toFixed(2)),
                                Number(item.minrating.toFixed(2)),
                            ];
                        }
                        this.grade_item_details_title = item.itemname;
                        this.grade_item_details_data = item_data;
                    },

                    find_first_grade_item(items) {
                        let item;
                        if (items.length) {
                            let count = items.length;
                            for (let i = 0; i < count; i++) {
                                if (items[i].maxrating > 0) {
                                    item = items[i];
                                    break;
                                }
                            }
                            if (!item) {
                                item = items[0];
                            }
                        }
                        return item;
                    },

                }
            })
        }

        return {
            init : init
        };
    });