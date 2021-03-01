define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/momenttimezone",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
        "local_fliplearning/modulesform"
    ],
    function(Vue, Vuetify, Axios, Moment, MomentTimezone, Pagination, ChartStatic, Pageheader, Modulesform) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', Pageheader);
            Vue.component('modulesform', Modulesform);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#student",
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
                        modules_dialog: false,
                        errors : [],

                        indicators: content.indicators,
                        modules_access_colors: content.modules_access_colors,
                        sessions_evolution_colors: content.sessions_evolution_colors,
                        user_grades_colors: content.user_grades_colors,
                        user: content.indicators.user,
                        weeks_sessions: content.indicators.sessions,
                        sessions_evolution_data: [],
                        user_grades_categories: [],
                        user_grades_data: [],
                        course_grades_data: [],
                        selected_sections: [],
                    }
                },
                beforeMount(){
                    this.set_modules_in_sections();
                    this.calculate_sessions_evolution();
                    this.calculate_user_grades()
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#student").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        return '';
                    },

                    set_modules_in_sections() {
                        let sectionsMap = new Map();
                        let sectionid = 0;
                        this.indicators.cms.forEach(cm => {
                            sectionid = Number(cm.section);
                            if (!sectionsMap.has(sectionid)) {
                                sectionsMap.set(sectionid, [cm]);
                            } else {
                                sectionsMap.get(sectionid).push(cm);
                            }
                        });
                        this.indicators.sections.forEach(section => {
                            sectionid = Number(section.sectionid);
                            section.sectionid = sectionid;
                            section.modules = (sectionsMap.has(sectionid)) ? sectionsMap.get(sectionid) : [];
                        });
                    },

                    build_modules_access_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {text: null};
                        chart.colors = this.modules_access_colors,
                        chart.xAxis = {
                            type: 'category',
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            title: {
                                enabled: true,
                                text: this.strings.modules_amount,
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            formatter: function () {
                                let module_text = (this.y == 1) ? vue.strings.module_label : vue.strings.modules_label;
                                return '<b>' + this.points[0].key + '</b>: ' + this.y + ' ' + module_text + '<br/>'
                                    + '<i>'+ vue.strings.modules_details + '<i/>';
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            vue.open_modules_modal(this.x);
                                        }
                                    }
                                }
                            }
                        },
                        chart.legend = {
                                enabled: false
                            };
                        chart.series = [{
                            colorByPoint: true,
                            data: [
                                {name: this.strings.modules_access_chart_series_viewed, y: this.user.cms.viewed},
                                {name: this.strings.modules_access_chart_series_complete, y: this.user.cms.complete},
                                {name: this.strings.modules_access_chart_series_total, y: this.user.cms.total}
                            ]
                        }];
                        return chart;
                    },

                    build_weeks_session_chart() {
                        let chart = new Object();
                        chart.title = {text: null};
                        chart.chart = {
                            type: 'heatmap',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
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
                            minColor: '#E0E0E0',
                            maxColor: '#118AB2'
                        };
                        chart.legend = {
                            layout: 'horizontal',
                            verticalAlign: 'bottom',
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let days = vue.weeks_sessions.weeks[this.point.y][this.point.x] || '';
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = vue.strings.sessions_text;
                                if (this.point.value == 1) {
                                    label = vue.strings.session_text;;
                                }
                                return '<b>' + yCategoryName + ' ' + xCategoryName + '</b>: '
                                    + this.point.value +' ' + label + '<br/>' + days;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.weeks_sessions.data,
                        }];
                        return chart;
                    },

                    build_sessions_evolution_chart() {
                        let chart = new Object();
                        chart.chart = {
                            zoomType: 'x',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {text: null};
                        chart.colors = this.sessions_evolution_colors,
                        chart.xAxis = {
                            type: 'datetime'
                        };
                        chart.yAxis = [{
                            allowDecimals: false,
                            title: { text: this.strings.sessions_evolution_chart_xaxis1 }
                        }, {
                            title: { text: this.strings.sessions_evolution_chart_xaxis2 },
                            opposite: true
                        }];
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function () {
                                let date_label = vue.calculate_timezone_date_string(this.x);
                                let text1 = (this.points[0]) ? vue.get_sessions_evolution_tooltip(this.points[0]) : '';
                                let text2 = (this.points[1]) ? vue.get_sessions_evolution_tooltip(this.points[1]) : '';
                                return `<small>${date_label}</small><br/>${text1}${text2}`;
                            }
                        };
                        chart.series = this.sessions_evolution_data;
                        return chart;
                    },

                    build_user_grades_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {text: null};
                        chart.colors = this.user_grades_colors,
                        chart.xAxis = {
                            crosshair: true,
                            categories: this.user_grades_categories,
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            max: 100,
                            labels: {
                                format: '{value} %',
                            },
                            title: { text: this.strings.user_grades_chart_yaxis }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function () {
                                let itemname = this.x;
                                let position = this.points[0].point.x;
                                let item = vue.user.gradeitems[position];
                                let header = `<small>${itemname}</small><br/>`;
                                let footer = `<i>(${vue.strings.user_grades_chart_view_activity})</i><br/>`;
                                let body = '';
                                if (item.gradecount == 0) {
                                    body = vue.strings.user_grades_chart_tooltip_no_graded;
                                } else {
                                    let text1 = (this.points[0]) ? vue.get_user_grades_tooltip(this.points[0], item) : '';
                                    let text2 = (this.points[1]) ? vue.get_user_grades_tooltip(this.points[1], item) : '';
                                    body = `${text1}${text2}${footer}`;
                                }
                                return `${header}${body}`;
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            let position = this.x;
                                            let item = vue.user.gradeitems[position];
                                            let url = `${M.cfg.wwwroot}/mod/${item.itemmodule}/view.php?id=${item.coursemoduleid}`;
                                            window.open(url, '_blank');
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.user_grades_data;
                        return chart;
                    },

                    calculate_sessions_evolution() {
                        let sessions_data = [], time_data = [];
                        let sumtime = 0, sumsessions = 0, time = 0, timestamp = 0;
                        this.user.sessions.forEach(session => {
                            timestamp = Number(session.start) * 1000;
                            time = (Number(session.duration)) / 60;
                            sumtime += time;
                            sumsessions++;
                            sessions_data.push({ x: timestamp, y: sumsessions });
                            time_data.push({ x: timestamp, y: sumtime });
                        });
                        this.sessions_evolution_data = [
                            { name: this.strings.sessions_evolution_chart_legend1, yAxis: 0, data: sessions_data },
                            { name: this.strings.sessions_evolution_chart_legend2, yAxis: 1, data: time_data },
                        ];
                    },

                    calculate_user_grades() {
                        let categories = [], course_grades = [], user_grades = [];
                        let user_grade = 0, user_name = this.user.firstname;
                        if (this.user.gradeitems) {
                            this.user.gradeitems.forEach(item => {
                                user_grade = (Number(item.finalgrade) * 100) / Number(item.grademax);
                                categories.push(item.itemname);
                                course_grades.push(item.average_percentage);
                                user_grades.push(user_grade);
                            });
                            this.user_grades_data = [
                                { name: user_name, data: user_grades },
                                { name: this.strings.user_grades_chart_legend, data: course_grades },
                            ];
                            this.user_grades_categories = categories;
                        }
                    },

                    calculate_timezone_date_string(timestamp) {
                        let dat, weekday, monthday, month, time;
                        if (Moment.tz.zone(this.timezone)) {
                            dat = Moment(timestamp).tz(this.timezone);
                            weekday = dat.day();
                            monthday = dat.date();
                            month = dat.month();
                            time = dat.format('HH:mm:ss');
                        } else {
                            let tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                            dat =  new Date(timestamp);
                            weekday = dat.getDay();
                            monthday = dat.getDate();
                            month = dat.getMonth();
                            time = `${dat.getHours()}:${dat.getMinutes()}:${dat.getSeconds()} (${tz})`;
                        }
                        weekday = this.strings.chart.weekdays[weekday];
                        month = this.strings.chart.shortMonths[month];
                        return `${weekday}, ${month} ${monthday}, ${time}`;
                    },

                    get_point_category_name(point, dimension) {
                        let series = point.series,
                            isY = dimension === 'y',
                            axis = series[isY ? 'yAxis' : 'xAxis'];
                        return axis.categories[point[isY ? 'y' : 'x']];
                    },

                    get_sessions_evolution_tooltip (point) {
                        let text = '', sessions, sessions_suffix, sessions_prefix, time_prefix, time;
                        if (point.colorIndex == 0) {
                            sessions = point.y;
                            sessions_suffix = (sessions == 1) ? vue.strings.session_text : vue.strings.sessions_text;
                            sessions_prefix = point.series.name;
                            text = `<b style="color: ${point.color}">${sessions_prefix}: </b>
                                     ${sessions} ${sessions_suffix}<br/>`;
                        } else {
                            time_prefix = point.series.name;
                            time = vue.convert_time(point.y * 60);
                            text = `<b style="color: ${point.color}">${time_prefix}: </b>
                                    ${time}<br/>`;
                        }
                        return text;
                    },

                    get_user_grades_tooltip (point, item) {
                        let serie_name = point.series.name, user_grade = 0;
                        let finalgrade = Number(item.finalgrade), average = Number(item.average), grademax = Number(item.grademax);
                        grademax = this.isInt(grademax) ? grademax : grademax.toFixed(2);
                        if (point.colorIndex == 0) {
                            user_grade = this.isInt(finalgrade) ? finalgrade : finalgrade.toFixed(2);
                        } else {
                            user_grade = this.isInt(average) ? average : average.toFixed(2);
                        }
                        return `<b style="color: ${point.color}">${serie_name}: </b>
                                     ${user_grade}/${grademax}<br/>`;
                    },

                    open_modules_modal(){
                        let sections = this.indicators.sections;
                        sections.forEach(section => {
                            section.modules.forEach(module => {
                                module.complete = false;
                                module.viewed = false;
                                module.interactions = 0;
                                let user_cm = this.user.cms.modules[`cm${module.id}`];
                                if (user_cm) {
                                    module.complete = user_cm.complete;
                                    module.viewed = user_cm.viewed;
                                    module.interactions = user_cm.interactions;
                                }
                            });
                        });

                        this.modules_dialog = true;
                        this.selected_sections = sections;
                    },

                    get_picture_url(userid){
                        return `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                    },

                    get_user_fullname(){
                        return `${this.user.firstname} ${this.user.lastname}`;
                    },

                    get_username(){
                        return `@${this.user.username}`;
                    },

                    see_profile () {
                        let id = this.user.id;
                        let url = M.cfg.wwwroot + '/user/view.php?id='+id+'&course='+vue.courseid;
                        window.open(url);
                    },

                    get_progress_percentage() {
                        return `${this.user.progress_percentage} %`;
                    },

                    get_progress_message(){
                        let module_label = this.strings.modules_label;
                        let finished_label = this.strings.finisheds_label;
                        if (this.user.cms.complete == 1) {
                            module_label = this.strings.module_label;
                            finished_label = this.strings.finished_label;
                        }
                        return `${this.user.cms.complete} ${module_label} ${finished_label} ${this.strings.of_conector} ${this.user.cms.total}`;
                    },

                    convert_time(time) {
                        time *= 60; // pasar los minutos a segundos
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

                    get_student_grade() {
                        let grade = this.user.coursegrade;
                        grade.finalgrade = Number(grade.finalgrade);
                        grade.maxgrade = Number(grade.maxgrade);
                        let student_grade = this.isInt(grade.finalgrade) ? grade.finalgrade : grade.finalgrade.toFixed(2);
                        let max_grade = this.isInt(grade.maxgrade) ? grade.maxgrade : grade.maxgrade.toFixed(2);
                        return `${student_grade}/${max_grade}`;
                    },

                    update_modules_dialog(value) {
                        this.modules_dialog = value;
                    },

                    isInt(n) {
                        return n % 1 === 0;
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