define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartstatic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartStatic, Pageheader) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartStatic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
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

                        indicators: content.indicators,
                        inverted_time: content.indicators.inverted_time,
                        hours_sessions: content.indicators.hours_sessions,
                        sections: content.indicators.sections,
                        sections_map: null,
                        week_progress: 0,
                        resource_access_categories: [],
                        resource_access_data: [],
                        modules_dialog: false,
                    }
                },
                beforeMount(){
                    this.create_section_map();
                    this.set_modules_in_sections();
                    this.calculate_resources_access();
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#work_sessions").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        return '';
                    },

                    build_inverted_time_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.inverted_time_chart_title,
                        };
                        chart.xAxis = {
                            type: 'category',
                            crosshair: true,
                        };
                        chart.yAxis = {
                            title: {
                                text: this.strings.inverted_time_chart_x_axis,
                            }
                        };
                        chart.tooltip = {
                            shared:true,
                            useHTML:true,
                            formatter: function () {
                                let category_name = this.points[0].key;
                                let time = vue.convert_time(this.y);
                                return `<b>${category_name}: </b>${time}`;
                                // return '<b>' + vue.strings.inverted_time + ': </b>'
                                //     + vue.inverted_time.inverted_time_converted + '<br/>'
                                //     + '<b>' + vue.strings.expected_time + ': </b>'
                                //     + vue.inverted_time.expected_time_converted + '<br/>';
                            }
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = [{
                            colorByPoint: true,
                            data: this.inverted_time.data
                        }];
                        return chart;
                    },

                    build_session_by_hours_chart() {
                        let chart = new Object();
                        chart.title = {
                            text: this.strings.hours_sessions_title,
                        };
                        chart.chart = {
                            type: 'heatmap',
                            marginTop: 40,
                            marginBottom: 80,
                            plotBorderWidth: 0,
                            backgroundColor: '#FAFAFA',
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
                                let xCategoryName = vue.get_point_category_name(this.point, 'x');
                                let yCategoryName = vue.get_point_category_name(this.point, 'y');
                                let label = vue.strings.sessions_text;
                                if (this.point.value == 1) {
                                    label = vue.strings.session_text;
                                }
                                return '<b>' + xCategoryName + ' ' + yCategoryName + '</b>: '
                                    + this.point.value +' ' + label;
                            }
                        };
                        chart.series = [{
                            borderWidth: 2,
                            borderColor: '#FAFAFA',
                            data: this.hours_sessions,
                            dataLabels: {
                                enabled: false,
                            }
                        }];
                        return chart;
                    },

                    build_resources_access_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.resource_access_title,
                        };
                        chart.xAxis = {
                            categories: this.resource_access_categories,
                            crosshair: true,
                            title: {
                                text: this.strings.resource_access_x_axis
                            },
                        };
                        chart.yAxis = {
                            min: 0,
                            title: {
                                text: this.strings.resource_access_y_axis
                            },
                        };
                        chart.plotOptions = {
                            column: {
                                stacking: 'normal',
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            vue.modules_dialog = true;
                                        }
                                    }
                                }
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            useHTML: true,
                            footerFormat: `<i>${this.strings.modules_details}</i>`,
                        };
                        chart.series = this.resource_access_data;
                        return chart;
                    },

                    update_interactions(week){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "studentsessions",
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
                            if (response.status == 200 && response.data.ok) {
                                this.inverted_time = response.data.data.indicators.inverted_time;
                                this.hours_sessions = response.data.data.indicators.hours_sessions;
                                this.sections = response.data.data.indicators.sections;
                                this.set_modules_in_sections();
                                this.calculate_resources_access();
                            } else {
                                this.error_messages.push(this.strings.error_network);
                            }
                        }).catch((e) => {
                            console.log(e);
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    create_section_map() {
                        let sectionsMap = new Map();
                        let sectionid = 0;
                        this.indicators.course_cms.forEach(cm => {
                            sectionid = Number(cm.section);
                            if (!sectionsMap.has(sectionid)) {
                                sectionsMap.set(sectionid, [cm]);
                            } else {
                                sectionsMap.get(sectionid).push(cm);
                            }
                        });
                        this.sections_map = sectionsMap;
                    },

                    set_modules_in_sections() {
                        let sectionid;
                        this.sections.forEach(section => {
                            sectionid = Number(section.sectionid);
                            section.sectionid = sectionid;
                            section.modules = this.sections_map.get(sectionid);
                        });
                    },

                    calculate_resources_access() {
                        let modulesMap = new Map();
                        let moduleid, user_cm, mod, total_modules = 0, access_modules = 0;
                        let modules_names = this.strings.modules_names;
                        this.sections.forEach(section => {
                            section.modules.forEach(module => {
                                (!modulesMap.has(module.modname)) && modulesMap.set(module.modname,{complete:0,pending:0});
                                mod = modulesMap.get(module.modname);
                                moduleid = Number(module.id);
                                module.id = moduleid;
                                module.complete = false;
                                module.viewed = false;
                                module.interactions = 0;

                                user_cm = this.indicators.user_cms[`cm${module.id}`];
                                if (user_cm) {
                                    module.complete = user_cm.complete;
                                    module.viewed = user_cm.viewed;
                                    module.interactions = user_cm.interactions;
                                    (user_cm.complete) ? mod.complete++ : mod.pending++;
                                    (user_cm.complete) && access_modules++;
                                } else {
                                    mod.pending++
                                }
                                total_modules++;
                            });
                        });
                        let categories = [], complete_data = [], pending_data = [];
                        modulesMap.forEach(function(value, key) {
                            categories.push(modules_names[key] || key);
                            complete_data.push(value.complete);
                            pending_data.push(value.pending);
                        });
                        this.resource_access_categories = categories;
                        this.resource_access_data = [
                            { name: this.strings.resource_access_legend1, data: complete_data },
                            { name: this.strings.resource_access_legend2, data: pending_data },
                        ];
                        this.week_progress = Math.floor((access_modules*100)/total_modules);
                    },

                    get_progress_percentage() {
                        return `${this.week_progress} %`;
                    },

                    get_point_category_name(point, dimension) {
                        let series = point.series,
                            isY = dimension === 'y',
                            axis = series[isY ? 'yAxis' : 'xAxis'];
                        return axis.categories[point[isY ? 'y' : 'x']];
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

                    convert_time(time) {
                        time *= 3600; // pasar las horas a segundos
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