define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/momenttimezone",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
        "local_fliplearning/modulesform",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Moment, MomentTimezone, Pagination, ChartDynamic, PageHeader, EmailForm, ModulesForm, HelpDialog) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', PageHeader);
            Vue.component('emailform', EmailForm);
            Vue.component('modulesform', ModulesForm);
            Vue.component('helpdialog', HelpDialog);
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
                        modules_access_colors: content.modules_access_colors,
                        week_modules_colors: content.week_modules_colors,
                        sessions_evolution_colors: content.sessions_evolution_colors,
                        user_grades_colors: content.user_grades_colors,
                        selected_cluster: [],
                        cluster_users: [],
                        selected_user: null,
                        search: null,
                        week_modules_chart_data: [],
                        week_modules_chart_categories: [],
                        selected_sections: [],
                        sessions_evolution_data: [],
                        user_grades_categories: [],
                        user_grades_data: [],
                        modules_dialog: false,

                        email_users: [],
                        email_dialog : false,
                        modulename : "",
                        moduleid : false,
                        email_strings: content.strings.email_strings,

                        help_dialog: false,
                        help_contents: [],
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
                        let contents = [];
                        contents.push({
                            title: this.strings.section_help_title,
                            description: this.strings.section_help_description,
                        });
                        return contents;
                    },

                    set_modules_in_sections() {
                        let sectionsMap = new Map();
                        let sectionid = 0, modules = [];
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
                            section.modules = (sectionsMap.has(sectionid)) ? sectionsMap.get(sectionid) : [];
                        });
                    },

                    change_cluster(users) {
                        let selected_users = [];
                        this.dropout.users.forEach(user => {
                            if (users.includes(user.id)) {
                                selected_users.push(user);
                            }
                        });
                        if (selected_users.length) {
                            this.cluster_users = selected_users;
                            let user = this.cluster_users[0];
                            this.change_user(user);
                        } else {
                            this.cluster_users = [];
                            this.selected_user = null;
                        }
                    },

                    change_user(user) {
                        this.selected_user = user;
                        this.calculate_week_modules_access();
                        this.calculate_sessions_evolution();
                        this.calculate_user_grades();
                    },

                    calculate_week_modules_access() {
                        let sectionid = 0, moduleid = 0, weekcompletecms = 0, weekviewedcms = 0;
                        let modules = [], completecms = [], viewedcms = [], categories = [];
                        let user_cm;
                        this.dropout.weeks.forEach(week => {
                            weekcompletecms = 0, weekviewedcms = 0;
                            week.sections.forEach(section => {
                                sectionid = Number(section.sectionid);
                                section.sectionid = sectionid;

                                modules = this.sections_modules(sectionid);
                                modules.forEach(module => {
                                    moduleid = Number(module.id);
                                    module.id = moduleid;

                                    user_cm = this.selected_user.cms.modules[`cm${module.id}`];
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
                        if (this.selected_user.gradeitems) {
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
                        }
                    },

                    build_modules_access_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = { text: null};
                        chart.colors = this.modules_access_colors;
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
                            useHTML:true,
                            formatter: function () {
                                let module_text = vue.capitalizeFirstLetter(vue.strings.modules_label);
                                return '<small>' + this.points[0].key + ' </small><br/>'
                                    + '<b style="color: ' + this.points[0].color + ';">• </b>'
                                    + '<b>' +module_text + '</b>: ' + this.y + '<br/>'
                                    + '<small>'+ vue.strings.modules_details + '</small>';
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
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {text: null};
                        chart.colors = this.week_modules_colors;
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
                                    let viewed_series_name = this.points[0].series.name;
                                    text1 = `<b style="color: ${this.points[0].color};">• </b>
                                            <b>${viewed_series_name}: </b>${this.points[0].y}<br/>`;
                                }
                                if (this.points[1]) {
                                    let completed_series_name = this.points[1].series.name;
                                    text2 = `<b style="color: ${this.points[1].color};">• </b>
                                            <b>${completed_series_name}: </b>${this.points[1].y}<br/>`;
                                }
                                return `<small>${this.x}</small><br/> ${text1}${text2} 
                                        <small>${vue.strings.modules_details}</small>`;
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
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {text: null};
                        chart.colors = this.sessions_evolution_colors;
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
                        chart.colors = this.user_grades_colors;
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
                                let item = vue.selected_user.gradeitems[position];
                                let header = `<small>${itemname}</small><br/>`;
                                let footer = `<small>(${vue.strings.user_grades_chart_view_activity})</small><br/>`;
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
                        let text = '', sessions, sessions_prefix, time_prefix, time;
                        if (point.colorIndex == 0) {
                            sessions = point.y;
                            sessions_prefix = point.series.name;
                            text = `<b style="color: ${point.color};">• </b>
                                    <b>${sessions_prefix}: </b>${sessions}<br/>`;
                        } else {
                            time_prefix = point.series.name;
                            time = this.convert_time(point.y * 60);
                            text = `<b style="color: ${point.color};">• </b>
                                    <b>${time_prefix}: </b>
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
                        return `<b style="color: ${point.color};">• </b>
                                <b>${serie_name}: </b>${user_grade}/${grademax}<br/>`;
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

                        this.selected_sections = sections;
                        this.modules_dialog = true;
                    },

                    sections_modules(sectionid) {
                        let modules = [];
                        let sections = this.dropout.sections;
                        for (let i = 0; i < sections.length; i++) {
                            if (sections[i].sectionid == sectionid) {
                                if (sections[i].modules) {
                                    modules = sections[i].modules;
                                }
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

                    get_picture_url(userid){
                        return `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
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
                        let finished_label = this.strings.finished_resources;
                        return `${finished_label}: ${this.selected_user.cms.complete} ${this.strings.of_conector} ${this.selected_user.cms.total}`;
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

                    get_sendmail_user_text() {
                        return `${this.strings.send_mail_to_user} ${this.selected_user.firstname}`;
                    },

                    sendmail(type) {
                        this.strings.email_strings.subject = this.strings.email_strings.subject_prefix;
                        this.modulename = "course";
                        this.moduleid = this.courseid;
                        if (type == 1) {
                            this.email_users = [this.selected_user];
                            this.email_dialog = true;
                        } else if (type == 2) {
                            this.email_users = this.cluster_users;
                            this.email_dialog = true;
                        }
                    },

                    update_email_dialog (value) {
                        this.email_dialog = value;
                    },

                    update_modules_dialog (value) {
                        this.modules_dialog = value;
                    },

                    isInt(n) {
                        return n % 1 === 0;
                    },

                    capitalizeFirstLetter (string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    },

                    generate_dropout_data(){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "dropoutdata",
                            userid : this.userid,
                            courseid : this.courseid,
                            profile : this.render_has,
                        }
                        Axios({
                            method:'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params : data,
                        }).then((response) => {
                            if (response.status == 200 && response.data.ok) {
                                location.reload();
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

                    open_chart_help(chart) {
                        let contents = [];
                        if (chart == "group_students") {
                            contents.push({
                                title: this.strings.group_students_help_title,
                                description: this.strings.group_students_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.group_students_help_description_p2,
                            });
                        } else if (chart == "modules_access") {
                            contents.push({
                                title: this.strings.modules_access_help_title,
                                description: this.strings.modules_access_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.modules_access_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.modules_access_help_description_p3,
                            });
                        } else if (chart == "week_modules") {
                            contents.push({
                                title: this.strings.week_modules_help_title,
                                description: this.strings.week_modules_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.week_modules_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.week_modules_help_description_p3,
                            });
                        } else if (chart == "sessions_evolution") {
                            contents.push({
                                title: this.strings.sessions_evolution_help_title,
                                description: this.strings.sessions_evolution_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.sessions_evolution_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.sessions_evolution_help_description_p3,
                            });
                        } else if (chart == "user_grades") {
                            contents.push({
                                title: this.strings.user_grades_help_title,
                                description: this.strings.user_grades_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.user_grades_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.user_grades_help_description_p3,
                            });
                        }
                        this.help_contents = contents;
                        if (this.help_contents.length) {
                            this.help_dialog = true;
                        }
                    },

                    update_help_dialog (value) {
                        this.help_dialog = value;
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