define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform"
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, Pageheader, Emailform) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', Pageheader);
            Vue.component('emailform', Emailform);
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

                        grade_item_users: null,
                        selected_users: [],
                        dialog : false,
                        modulename : "",
                        moduleid : false,
                        email_strings: content.strings.email_strings,
                    }
                },
                beforeMount(){
                    if (this.grades.categories.length) {
                        this.default_category = this.grades.categories[0];
                        this.calculate_chart_items_average(this.default_category.items);
                        let item = this.find_first_grade_item(this.default_category.items);
                        this.update_detail_charts(item);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#grades").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    change_category(items) {
                        this.calculate_chart_items_average(items);
                        let item = this.find_first_grade_item(items);
                        this.update_detail_charts(item);
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
                                        }
                                    }
                                }
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            formatter: function() {
                                let position = this.points[0].point.x;
                                let value = this.y;
                                let item = vue.selected_items[position];
                                let count = item.gradecount;
                                let name = this.x;
                                let view_details = vue.strings.view_details;
                                let average = Number(item.average);
                                let students_label = vue.strings.grades_tooltip_students;
                                if (count == 1) {
                                    students_label = vue.strings.grades_tooltip_student;
                                }
                                value = vue.isInt(value) ? value : value.toFixed(2);
                                average = vue.isInt(average) ? average : average.toFixed(2);
                                let grademax = item.grademax;
                                let text = '<b>' + name + '<b> <br/>' +
                                    vue.strings.grades_tooltip_average + ': ' + average + ' (' + value + ' %)<br/>' +
                                    vue.strings.grades_tooltip_grade + ': ' + grademax + '<br/>' +
                                    count + ' ' + students_label + ' ' + vue.grades.student_count + '<br/>' +
                                    '<i>' + view_details + '</i>';
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
                            type: 'category',
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            shared: true,
                            formatter: function() {
                                let category = this.points[0].key;
                                let name = vue.selected_item.itemname;
                                let maxgrade = vue.selected_item.grademax;
                                let grade = this.y;
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
                            colorByPoint: true,
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
                                let send_mail = vue.strings.send_mail;
                                let name = this.x;
                                let value = this.y;
                                let students_label = vue.strings.students_text;
                                if (value == 1) {
                                    students_label = vue.strings.student_text;
                                }
                                let text = '<b>' + prefix + ': </b> '+ name + ' <br/>'
                                    + value + ' ' + students_label + ' ' + suffix + ' <br/>'
                                    + '<i>' + send_mail + '</i>';
                                return text;
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                stacking: 'normal',
                                borderWidth: 1,
                                pointPadding: 0,
                                groupPadding: 0,
                            },
                            column:{
                                point:{
                                    events: {
                                        click: function () {
                                            let position = this.x;
                                            vue.selected_users = vue.grade_item_users[position];
                                            vue.email_strings.subject = vue.email_strings.subject_prefix
                                                + " - " + vue.selected_item.itemname;
                                            vue.dialog = true;
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
                        this.modulename = item.itemmodule;
                        this.moduleid = item.coursemoduleid;
                        this.grade_item_title = item.itemname;
                        this.calculate_chart_item_grade_detail(item);
                        this.calculate_chart_item_grades_distribution(item);
                    },

                    calculate_chart_item_grade_detail(item) {
                        this.selected_item = item;
                        let item_data = [{
                            name: this.strings.grades_best_grade,
                            y: Number(item.maxrating) || 0
                        },{
                            name: this.strings.grades_average_grade,
                            y: Number(item.average) || 0
                        }, {
                            name: this.strings.grades_worst_grade,
                            y: Number(item.minrating) || 0
                        }];
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
                        this.grade_item_users = users;
                        this.grade_item_distribution_categories = categories,
                        this.grade_item_distribution_data = values;
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

                    isInt(n) {
                        return n % 1 === 0;
                    },

                    update_dialog (value) {
                        this.dialog = value;
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