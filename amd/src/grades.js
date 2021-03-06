define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Pagination, ChartDynamic, PageHeader, EmailForm, HelpDialog) {
        "use strict";

        function init(content) {
            // console.logs(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', PageHeader);
            Vue.component('emailform', EmailForm);
            Vue.component('helpdialog', HelpDialog);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#teacher_grades",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings: content.strings,
                        groups: content.groups,
                        userid: content.userid,
                        courseid: content.courseid,
                        timezone: content.timezone,
                        render_has: content.profile_render,
                        loading: false,
                        errors: [],
                        pages: content.pages,

                        grades: content.grades,
                        grade_items_average_colors: content.grade_items_average_colors,
                        item_grades_details_colors: content.item_grades_details_colors,
                        item_grades_distribution_colors: content.item_grades_distribution_colors,
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
                        dialog: false,
                        modulename: "",
                        moduleid: false,
                        email_strings: content.strings.email_strings,
                        emailComponent: "",
                        emailTarget: "",

                        help_dialog: false,
                        help_contents: [],

                        pluginSectionName: "teacher_grades",
                        grade_items_average_chart: "grade_items_average_chart",
                        item_grades_details_chart: "item_grades_details_chart",
                        item_grades_distribution_chart: "item_grades_distribution_chart",
                    }
                },
                beforeMount() {
                    if (this.grades.categories.length) {
                        this.default_category = this.grades.categories[0];
                        this.calculate_chart_items_average(this.default_category.items);
                        let item = this.find_first_grade_item(this.default_category.items);
                        this.update_detail_charts(item);
                    };
                },
                mounted() {
                    document.querySelector("#teacher_grades_loader").style.display = "none";
                    document.querySelector("#teacher_grades").style.display = "block";
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

                    change_category(items) {
                        this.calculate_chart_items_average(items);
                        let item = this.find_first_grade_item(items);
                        this.update_detail_charts(item);

                        this.saveInteraction ('grade_item_selector', "selected", "grade_item", 11);
                    },

                    build_grade_items_average_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null
                        };
                        chart.colors = this.grade_items_average_colors;
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
                                        click: function() {
                                            let position = this.x;
                                            let item = vue.selected_items[position];
                                            vue.update_detail_charts(item);

                                            vue.saveInteraction (
                                                vue.grade_items_average_chart,
                                                "selected",
                                                "grade_item",
                                                12
                                            );
                                        }
                                    }
                                }
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                let position = this.points[0].point.x;
                                let value = this.y;
                                let item = vue.selected_items[position];
                                let count = item.gradecount;
                                let name = this.x;
                                let view_details = vue.strings.view_details;
                                let average = Number(item.average);
                                let color = this.points[0].color;
                                value = vue.isInt(value) ? value : value.toFixed(2);
                                average = vue.isInt(average) ? average : average.toFixed(2);
                                let grademax = item.grademax;
                                return '<small>' + name + '</small><br/>' +
                                    '<b style="color: ' + color + ';">• </b>' +
                                    vue.strings.grades_tooltip_average + ': ' + average + '/' + grademax + ' (' + value + ' %)<br/>' +
                                    '<b style="color: ' + color + ';">• </b>' +
                                    vue.strings.graded_students + ': ' + count + ' ' +
                                    vue.strings.of_conector + ' ' + vue.grades.student_count + '<br/>' +
                                    '<small>' + view_details + '</small>';
                            }
                        };
                        chart.yAxis = [{
                            allowDecimals: false,
                            max: 100,
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
                        return chart;
                    },

                    build_item_grades_details_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.item_grades_details_colors;
                        chart.xAxis = {
                            type: 'category',
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function() {
                                let category = this.points[0].key;
                                let name = vue.selected_item.itemname;
                                let maxgrade = vue.selected_item.grademax;
                                let grade = this.y;
                                let color = this.points[0].color;
                                grade = vue.isInt(grade) ? grade : grade.toFixed(2);
                                return '<small>' + name + '</small> <br/>' +
                                    '<b style="color: ' + color + ';">• </b>' +
                                    category + ': ' + grade + '/' + maxgrade + '<br/>';
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
                        return chart;
                    },

                    build_item_grades_distribution_chart() {
                        let chart = new Object();
                        chart.chart = {
                            backgroundColor: null,
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.item_grades_distribution_colors;
                        chart.xAxis = {
                            categories: this.grade_item_distribution_categories
                        };
                        chart.yAxis = [{
                            title: {
                                text: this.strings.grades_distribution_yaxis_title,
                            },
                            allowDecimals: false,
                        }];
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            useHTML: true,
                            formatter: function() {
                                let prefix = vue.strings.grades_distribution_tooltip_prefix;
                                let send_mail = vue.strings.send_mail;
                                let name = this.x.split('<br/>')[0] || 'Rango';
                                let value = this.y;
                                let students_label = vue.capitalizeFirstLetter(vue.strings.students_text);
                                return '<small>' + prefix + ': ' + name + '</small><br/>' +
                                    '<b style="color: ' + this.color + ';">• </b>' +
                                    students_label + ': ' + value + ' <br/>' +
                                    '<small>' + send_mail + '</small>';
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                stacking: 'normal',
                                borderWidth: 1,
                                pointPadding: 0,
                                groupPadding: 0,
                            },
                            column: {
                                point: {
                                    events: {
                                        click: function() {
                                            vue.emailComponent = "";
                                            vue.emailTarget = "";

                                            let position = this.x;
                                            vue.selected_users = vue.grade_item_users[position];
                                            vue.email_strings.subject = vue.email_strings.subject_prefix +
                                                " - " + vue.selected_item.itemname;

                                            let target = vue.grade_item_distribution_categories[position].split("<br/>")[1];
                                            target = `${target.replace(/ /g, '_').toLowerCase()}_range_users`;

                                            vue.emailComponent = vue.item_grades_distribution_chart;
                                            vue.emailTarget = target;

                                            vue.dialog = true;
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = [{
                            type: 'column',
                            data: this.grade_item_distribution_data
                        }, {
                            type: 'spline',
                            data: this.grade_item_distribution_data,
                            marker: {
                                lineWidth: 1,
                            }
                        }];
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

                    update_detail_charts(item) {
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
                        }, {
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
                            `${smaller} 50%`
                        ];
                        let values = [0, 0, 0, 0, 0, 0];
                        let users = [
                            [],
                            [],
                            [],
                            [],
                            [],
                            []
                        ];
                        if (item) {
                            let weights = [0.9, 0.8, 0.7, 0.6, 0.5, 0];
                            let ranges = [];
                            let grademax = item.grademax;
                            let limit = grademax;
                            weights.forEach(weight => {
                                let grade = grademax * weight;
                                ranges.push({ max: limit, min: grade, count: 0 });
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

                    capitalizeFirstLetter(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    },

                    update_dialog(value) {
                        this.dialog = value;
                    },

                    openChartHelp(chart) {
                        let contents = [];
                        if (chart == this.grade_items_average_chart) {
                            contents.push({
                                title: this.strings.grade_items_average_help_title,
                                description: this.strings.grade_items_average_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.grade_items_average_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.grade_items_average_help_description_p3,
                            });
                        } else if (chart == this.item_grades_details_chart) {
                            contents.push({
                                title: this.strings.item_grades_details_help_title,
                                description: this.strings.item_grades_details_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.item_grades_details_help_description_p2,
                            });
                        } else if (chart == this.item_grades_distribution_chart) {
                            contents.push({
                                title: this.strings.item_grades_distribution_help_title,
                                description: this.strings.item_grades_distribution_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.item_grades_distribution_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.item_grades_distribution_help_description_p3,
                            });
                        }
                        this.help_contents = contents;
                        if (this.help_contents.length) {
                            this.help_dialog = true;
                            this.saveInteraction (chart, "viewed", "chart_help_dialog", 7);
                        }
                    },

                    update_help_dialog(value) {
                        this.help_dialog = value;
                    },

                    saveInteraction (component, interaction, target, interactiontype) {
                        let data = {
                            action : "saveinteraction",
                            pluginsection : this.pluginSectionName,
                            component,
                            interaction,
                            target,
                            url: window.location.href,
                            interactiontype,
                            courseid : this.courseid,
                            userid : this.userid,
                        };
                        Axios({
                            method:'get',
                            url: `${M.cfg.wwwroot}/local/fliplearning/ajax.php`,
                            params : data,
                        }).then((r) => {}).catch((e) => {});
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