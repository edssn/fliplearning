define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/momenttimezone",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, MomentTimezone, Pagination, ChartDynamic, Pageheader) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#dropout",
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

                        dropout: content.dropout,
                        selected_cluster: [],
                        cluster_users: [],
                        selected_user: {},
                        search: null,
                        week_modules_chart_data: [],
                        week_modules_chart_categories: [],
                        selected_sections: [],
                        sessions_evolution_data: [],
                        user_grades_categories: [],
                        user_grades_data: [],
                        course_grades_data: [],
                        dialog: false,
                    }
                },
                beforeMount(){
                    if (this.dropout.clusters.length) {
                        this.set_modules_in_sections();
                        this.selected_cluster = this.dropout.clusters[0];
                        this.change_cluster(this.selected_cluster.users);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#dropout").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    set_modules_in_sections() {
                        let sectionsMap = new Map();
                        let sectionid = 0;
                        this.dropout.cms.forEach(cm => {
                            sectionid = Number(cm.section);
                            if (!sectionsMap.has(sectionid)) {
                                sectionsMap.set(sectionid, [cm]);
                            } else {
                                sectionsMap.get(sectionid).push(cm);
                            }
                        });
                        this.dropout.sections.forEach(section => {
                            sectionid = Number(section.sectionid);
                            section.sectionid = sectionid;
                            section.modules = sectionsMap.get(sectionid);
                        });
                    },

                    change_cluster(users) {
                        let selected_users = [];
                        this.dropout.users.forEach(user => {
                            if (users.includes(user.id)) {
                                selected_users.push(user);
                            }
                        });
                        this.cluster_users = selected_users;
                        let user = this.cluster_users[0] || {};
                        this.change_user(user);
                    },

                    build_modules_access_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.subtitle = {
                            text: `${this.selected_user.firstname} ${this.selected_user.lastname}`,
                        };
                        chart.title = {
                            text: this.strings.modules_access_chart_title,
                        };
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
                                {name: this.strings.modules_access_chart_series_viewed, y: this.selected_user.cms.viewed},
                                {name: this.strings.modules_access_chart_series_complete, y: this.selected_user.cms.complete},
                                {name: this.strings.modules_access_chart_series_total, y: this.selected_user.cms.total}
                            ]
                        }];
                        return chart;
                    },

                    build_week_modules_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.subtitle = {
                            text: `${this.selected_user.firstname} ${this.selected_user.lastname}`,
                        };
                        chart.title = {
                            text: this.strings.week_modules_chart_title,
                        };
                        chart.xAxis = {
                            categories: this.week_modules_chart_categories,
                            title: {
                                text: null
                            },
                            crosshair: true
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            title: {
                                text: this.strings.modules_amount,
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function () {
                                let text1 = '', text2 = '';
                                if (this.points[0]) {
                                    let module_text_viewed = (this.points[0].y == 1) ? vue.strings.module_label : vue.strings.modules_label;
                                    let viewed_series_name = this.points[0].series.name;
                                    text1 = `<b style="color: ${this.points[0].color}">${viewed_series_name}: </b>
                                            ${this.points[0].y} ${module_text_viewed}<br/>`;
                                }
                                if (this.points[1]) {
                                    let module_text_completed = (this.points[1].y == 1) ? vue.strings.module_label : vue.strings.modules_label;
                                    let completed_series_name = this.points[1].series.name;
                                    text2 = `<b style="color: ${this.points[1].color}">${completed_series_name}: </b>
                                            ${this.points[1].y} ${module_text_completed}<br/>`;
                                }
                                return `${this.x} <br/> ${text1}${text2} <i>${vue.strings.modules_details}<i/>`;
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            vue.open_modules_modal(this.colorIndex, this.x);
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.week_modules_chart_data;
                        return chart;
                    },

                    build_sessions_evolution_chart() {
                        let chart = new Object();
                        chart.chart = {
                            zoomType: 'x',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.subtitle = {
                            text: `${this.selected_user.firstname} ${this.selected_user.lastname}`,
                        };
                        chart.title = {
                            text: this.strings.sessions_evolution_chart_title,
                        };
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
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.user_grades_chart_title,
                        };
                        chart.subtitle = {
                            text: `${this.selected_user.firstname} ${this.selected_user.lastname}`,
                        };
                        chart.xAxis = {
                            crosshair: true,
                            categories: this.user_grades_categories,
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            title: { text: this.strings.user_grades_chart_yaxis }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            formatter: function () {
                                let itemname = this.x;
                                let position = this.points[0].point.x;
                                let item = vue.selected_user.gradeitems[position];
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
                                            console.log(this);
                                            let position = this.x;
                                            let item = vue.selected_user.gradeitems[position];
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

                    calculate_modules_access_by_week() {
                        let sectionid = 0, moduleid = 0, weekcompletecms = 0, weekviewedcms = 0;
                        let modules = [], completecms = [], viewedcms = [], categories = [];
                        let user_cm;
                        this.dropout.weeks.forEach(week => {
                            // console.log({week});
                            weekcompletecms = 0, weekviewedcms = 0;
                            week.sections.forEach(section => {
                                // console.log({section});
                                sectionid = Number(section.sectionid);
                                section.sectionid = sectionid;

                                modules = this.sections_modules(sectionid);
                                modules.forEach(module => {
                                    // console.log({module});
                                    moduleid = Number(module.id);
                                    module.id = moduleid;

                                    // user_cm = this.get_user_module(moduleid);
                                    user_cm = this.selected_user.cms.modules[`cm${module.id}`];
                                    // console.log({user_cm});
                                    if (user_cm) {
                                        (user_cm.complete) && weekcompletecms++;
                                        (user_cm.viewed) && weekviewedcms++;
                                    }

                                });

                            });
                            completecms.push(weekcompletecms);
                            viewedcms.push(weekviewedcms);
                            categories.push(`${week.name} ${(week.position + 1)}`);
                        });
                        // console.log({completecms, viewedcms});
                        this.week_modules_chart_categories = categories;
                        this.week_modules_chart_data = [
                            { name: this.strings.modules_access_chart_series_viewed, data: viewedcms },
                            { name: this.strings.modules_access_chart_series_complete, data: completecms }
                        ];
                    },

                    calculate_sessions_evolution() {
                        let sessions_data = [], time_data = [];
                        let sumtime = 0, sumsessions = 0, time = 0, timestamp = 0;
                        this.selected_user.sessions.forEach(session => {
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
                        let user_grade = 0, user_name = this.selected_user.firstname;
                        this.selected_user.gradeitems.forEach(item => {
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
                            text = `${hours}${h} ${minutes}${m}`;
                        } else if ((minutes >= 1)) {
                            text = `${minutes}${m} ${seconds}${s}`;
                        } else {
                            text = `${seconds}${s}`;
                        }
                        return text;
                    },

                    open_modules_modal(type, weekposition){
                        let sections = this.dropout.sections;
                        if (Number.isInteger(weekposition)) {
                            sections = [];
                            let section;
                            let week = this.dropout.weeks[weekposition];
                            week.sections.forEach(item => {
                                section = {
                                    sectionid: item.sectionid,
                                    name: item.name,
                                    modules: this.sections_modules(item.sectionid)
                                };
                                sections.push(section);
                            });
                        }
                        sections.forEach(section => {
                            section.modules.forEach(module => {
                                module.complete = false;
                                module.viewed = false;
                                module.interactions = 0;
                                let user_cm = this.selected_user.cms.modules[`cm${module.id}`];
                                if (user_cm) {
                                    module.complete = user_cm.complete;
                                    module.viewed = user_cm.viewed;
                                    module.interactions = user_cm.interactions;
                                }
                            });
                        });

                        this.dialog = true;
                        this.selected_sections = sections;
                    },

                    sections_modules(sectionid) {
                        let modules = [];
                        let sections = this.dropout.sections;
                        for (let i = 0; i < sections.length; i++) {
                            if (sections[i].sectionid == sectionid) {
                                modules = sections[i].modules;
                                break;
                            }
                        }
                        return modules;
                    },

                    get_user_module(moduleid) {
                        let module;
                        let cms = this.selected_user.cms.modules;
                        for (let i = 0; i < cms.length; i++) {
                            cms[i].id = Number(cms[i].id);
                            if (cms[i].id == moduleid) {
                                module = cms[i];
                                break;
                            }
                        }
                        return module;
                    },

                    table_headers(){
                        let headers = [
                            { text: '', value : 'id', align : 'center', sortable : false},
                            { text: this.strings.thead_name , value : 'firstname'},
                            { text: this.strings.thead_lastname , value : 'lastname'},
                            { text: this.strings.thead_progress , value : 'progress_percentage'},
                        ];
                        return headers;
                    },

                    change_user(user) {
                        this.selected_user = user;
                        console.log({user});
                        this.calculate_modules_access_by_week();
                        this.calculate_sessions_evolution();
                        this.calculate_user_grades()
                    },

                    get_picture_url(userid){
                        return `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                    },

                    get_module_icon(modname){
                        return `${M.cfg.wwwroot}/theme/image.php/boost/${modname}/1/icon`;
                    },

                    get_module_url(module){
                        return `${M.cfg.wwwroot}/mod/${module.modname}/view.php?id=${module.id}`;
                    },

                    get_interactions_number(interactions){
                        let interactions_text = (interactions == 1) ? this.strings.modules_interaction : this.strings.modules_interactions;
                        return `(${interactions} ${interactions_text})`;
                    },

                    get_user_fullname(){
                        return `${this.selected_user.firstname} ${this.selected_user.lastname}`;
                    },

                    get_username(){
                        return `@${this.selected_user.username}`;
                    },

                    see_profile () {
                        let id = this.selected_user.id;
                        let url = M.cfg.wwwroot + '/user/view.php?id='+id+'&course='+vue.courseid;
                        window.open(url);
                    },

                    get_progress_message(){
                        let module_label = this.strings.modules_label;
                        let finished_label = this.strings.finisheds_label;
                        if (this.selected_user.cms.complete == 1) {
                            module_label = this.strings.module_label;
                            finished_label = this.strings.finished_label;
                        }
                        return `${this.selected_user.cms.complete} ${module_label} ${finished_label} ${this.strings.of_conector} ${this.dropout.total_cms}`;
                    },

                    get_progress_percentage() {
                        return `${this.selected_user.progress_percentage} %`;
                    },

                    get_student_grade() {
                        let grade = this.selected_user.coursegrade;
                        grade.finalgrade = Number(grade.finalgrade);
                        grade.maxgrade = Number(grade.maxgrade);
                        let student_grade = this.isInt(grade.finalgrade) ? grade.finalgrade : grade.finalgrade.toFixed(2);
                        let max_grade = this.isInt(grade.maxgrade) ? grade.maxgrade : grade.maxgrade.toFixed(2);
                        return `${student_grade}/${max_grade}`;
                    },

                    isInt(n) {
                        return n % 1 === 0;
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