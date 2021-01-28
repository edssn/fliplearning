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

                        grade_item_title: "",
                        grade_item_details_categories: [],
                        grade_item_details_data: [],

                        grade_item_distribution_categories: [],
                        grade_item_distribution_data: [],

                        selected_item: null,
                        selected_users: null,
                    }
                },
                beforeMount(){
                    if (this.grades.categories.length) {
                        this.default_category = this.grades.categories[0];
                        this.calculate_chart_items_average(this.default_category.items);
                        let item = this.find_first_grade_item(this.default_category.items);
                        this.update_detail_charts(item);
                        // this.calculate_chart_item_grade_detail(item);
                        // this.calculate_chart_item_grades_distribution(item);
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
                        this.update_detail_charts(item);
                        // this.calculate_chart_item_grade_detail(item);
                        // this.calculate_chart_item_grades_distribution(item);
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
                                            vue.update_detail_charts(item);
                                            // vue.calculate_chart_item_grade_detail(item);
                                            // vue.calculate_chart_item_grades_distribution(item);
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
                                let average = Number(item.average).toFixed(2);
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
                            text: this.grade_item_title,
                        };
                        chart.subtitle = {
                            text: this.strings.grades_details_subtitle,
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
                                grade = Number(grade);
                                grade = vue.isInt(grade) ? grade : grade.toFixed(2);
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

                    build_chart_item_grades_distribution() {
                        let chart = new Object();
                        chart.chart = {
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.grade_item_title,
                        };
                        chart.subtitle = {
                            text: this.strings.grades_distribution_subtitle,
                        };
                        chart.xAxis = {
                            categories: this.grade_item_distribution_categories
                        };
                        chart.yAxis = [{
                            title: {
                                text: this.strings.grades_distribution_yaxis_title,
                            }
                        }];
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            formatter: function() {
                                let prefix = vue.strings.grades_distribution_tooltip_prefix;
                                let suffix = vue.strings.grades_distribution_tooltip_suffix;
                                let name = this.x;
                                let value = this.y;
                                let text = '<b>' + prefix + '</b> '+ name + ' <br/>'
                                    + value + ' ' + suffix;
                                return text;
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                stacking: 'normal',
                                borderWidth: 1,
                                pointPadding: 0,
                                groupPadding: 0,
                                point: {
                                    events: {
                                        click: function () {
                                            let position = this.x;
                                            console.log(vue.selected_users[position]);
                                            console.log(vue.selected_users);
                                            console.log(this);
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = [{
                            type: 'column',
                            name: 'Jane',
                            data: this.grade_item_distribution_data
                        }, {
                            type: 'spline',
                            name: 'Average',
                            data: this.grade_item_distribution_data,
                            marker: {
                                lineWidth: 1,
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

                    update_detail_charts (item) {
                        this.grade_item_title = item.itemname;
                        this.calculate_chart_item_grade_detail(item);
                        this.calculate_chart_item_grades_distribution(item);
                    },

                    calculate_chart_item_grade_detail(item) {
                        this.selected_item = item;
                        let item_data = [0, 0, 0];
                        if (item) {
                            item_data = [
                                Number(item.maxrating),
                                Number(item.average),
                                Number(item.minrating),
                            ];
                        }
                        this.grade_item_details_data = item_data;
                    },

                    calculate_chart_item_grades_distribution(item) {
                        let greater = this.strings.grades_greater_than;
                        let smaller = this.strings.grades_smaller_than;
                        let categories = [
                            `${greater} 90%`,
                            `${greater} 80%`,
                            `${greater} 70%`,
                            `${greater} 60%`,
                            `${greater} 50%`,
                            `${smaller} 50%`];
                        let values = [0, 0, 0, 0, 0, 0];
                        let users = [[], [], [], [], [], []];
                        if (item) {
                            let weights = [0.9, 0.8, 0.7, 0.6, 0.5, 0];
                            let ranges = [];
                            let grademax = item.grademax;
                            let limit = grademax;
                            weights.forEach(weight => {
                                let grade = grademax * weight;
                                ranges.push({ max: limit, min: grade, count: 0});
                                limit = grade - 0.1;
                            });

                            item.grades.forEach(grade => {
                                ranges.forEach((range, index) => {
                                    if (grade.rawgrade >= range.min && grade.rawgrade <= range.max) {
                                        range.count++;
                                        users[index].push(grade.user);
                                    }
                                });
                            });

                            values = [];
                            ranges.forEach((range, index) => {
                                let max = this.isInt(range.max) ? range.max : range.max.toFixed(1);
                                let min = this.isInt(range.min) ? range.min : range.min.toFixed(1);
                                let label = `${max} - ${min}<br/>${categories[index]}`;
                                categories[index] = label;
                                values.push(range.count);
                            });
                        }
                        this.selected_users = users;
                        this.grade_item_distribution_categories = categories,
                        this.grade_item_distribution_data = values;
                    },

                    isInt(n) {
                        return n % 1 === 0;
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