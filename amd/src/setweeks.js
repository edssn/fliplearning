define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/sortablejs",
        "local_fliplearning/draggable",
        "local_fliplearning/datepicker",
        "local_fliplearning/moment",
        "local_fliplearning/alertify",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Sortable, Draggable, Datepicker, Moment, Alertify, Pageheader) {
        "use strict";

        function init(content) {
            console.log(content);
            content = add_collapsabled_property_to_weeks(content);
            Vue.use(Vuetify);
            Vue.component('draggable', Draggable);
            Vue.component('datepicker', Datepicker);
            Vue.component('pageheader', Pageheader);
            const app = new Vue({
                delimiters: ["[[", "]]"],
                el: "#setweeks",
                vuetify: new Vuetify(),
                data: {
                    display_settings: false,
                    settings: content.settings,
                    new_group: false,
                    scroll_mode: false,
                    weeks_started_at: new Date(Moment(content.weeks[0].weekstart)),
                    strings: content.strings,
                    sections: content.sections,
                    courseid: content.courseid,
                    userid: content.userid,
                    raw_weeks: content.weeks,
                    disabled_dates: {
                        days: [0, 2, 3, 4, 5, 6]
                    },
                    saving_loader: false,
                    error_messages: [],
                    save_successful: false
                },
                mounted() {
                    document.querySelector("#setweeks-loader").style.display = "none";
                    document.querySelector("#setweeks").style.display = "block";
                },
                computed: {
                    weeks() {
                        for (let i = 0; i < this.raw_weeks.length; i++) {
                            let week = this.raw_weeks[i];
                            if (i == 0) {
                                var start_weeks = Moment(new Date(this.weeks_started_at)).format('YYYY-MM-DD');
                                var start_weeks = this.weeks_started_at;
                                week.weekstart = start_weeks;
                                week.weekend = this.get_end_week(this.weeks_started_at);
                            } else {
                                week.weekstart = this.get_start_week(this.raw_weeks[i - 1].weekend);
                                week.weekend = this.get_end_week(week.weekstart);
                            }
                        }
                        return this.raw_weeks;
                    }
                },
                methods: {
                    section_name(section) {
                        let name = null;
                        if (typeof (section.section_name) != 'undefined' && section.section_name.length > 0) {
                            name = section.section_name;
                        } else {
                            name = section.name;
                        }
                        return name;
                    },

                    section_exist(section) {
                        let exist = true;
                        if (typeof (section) != 'undefined' && typeof (section.exists) != 'undefined' && section.exists == false) {
                            exist = false;
                        }
                        return exist;
                    },

                    format_name(name, position) {
                        return name + " " + (position + 1);
                    },

                    customFormatter(date) {
                        let weeks_start_at = Moment(date).format('YYYY-MM-DD');
                        return weeks_start_at;
                    },

                    add_week() {
                        this.raw_weeks.push({
                            name: this.strings.week,
                            position: (this.weeks.length + 1),
                            start: null,
                            end: null,
                            collapsabled: false,
                            hours_dedications: 0,
                            removable: true,
                            sections: []
                        });
                    },

                    has_items(array) {
                        return array.length > 0;
                    },

                    remove_week(week, index) {
                        if (index == 0) {
                            return null;
                        }
                        this.close_delete_confirm();
                        for (let i = 0; i < week.sections.length; i++) {
                            this.sections.push(week.sections[i]);
                        }
                        let element_index = this.raw_weeks.indexOf(week);
                        this.raw_weeks.splice(element_index, 1);
                    },

                    ask_delete_confirm() {
                        this.delete_confirm = true;
                    },

                    close_delete_confirm() {
                        this.delete_confirm = false;
                    },

                    get_start_week(pass_week) {
                        var start_date = Moment(Moment(pass_week).add(1, 'days')).format('YYYY-MM-DD');
                        return start_date;
                    },

                    get_end_week(start_week) {
                        let end_date = Moment(Moment(start_week).add(6, 'days')).format('YYYY-MM-DD');
                        return end_date;
                    },

                    get_date_next_day(requested_day, date, output_format = null) {
                        requested_day = requested_day.toLowerCase();
                        let current_day = Moment(date).format('dddd').toLowerCase();
                        while (current_day != requested_day) {
                            date = Moment(date).add(1, 'days');
                            current_day = Moment(date).format('dddd').toLowerCase();
                        }
                        if (output_format) {
                            date = date.format(output_format);
                        } else {
                            if (typeof (date) != 'number') {
                                date = parseInt(date.format("x"));
                            }
                        }
                        return date;
                    },

                    position(index) {
                        index++;
                        return `${index} - `;
                    },

                    save_changes() {
                        this.save_successful = false;
                        this.error_messages = [];
                        if (this.empty_weeks()) {
                            this.saving_loader = false;
                            Alertify.error(this.strings.error_empty_week);
                            this.error_messages.push(this.strings.error_empty_week);
                            return false;
                        }
                        if (this.weeks_deleted_from_course()) {
                            this.saving_loader = false;
                            this.error_messages.push(this.strings.error_section_removed);
                            return false;
                        }

                        Alertify.confirm(this.strings.save_warning_content,
                            ()=>{
                                this.saving_loader = true;
                                var weeks = this.weeks;
                                weeks[0].weekstart = Moment(weeks[0].weekstart).format("YYYY-MM-DD");
                                var data = {
                                    action: "saveconfigweek",
                                    userid: this.userid,
                                    courseid: this.courseid,
                                    newgroup: this.new_group,
                                    weeks: this.minify_query(weeks) // Stringify is a hack to clone object :D
                                };

                                Axios({
                                    method: 'get',
                                    url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                                    params: data,
                                }).then((response) => {
                                    if (response.status == 200 && response.data.valid) {
                                        this.settings = response.data.data.settings;
                                        Alertify.success(this.strings.save_successful);
                                        this.save_successful = true;
                                    } else {
                                        Alertify.error(this.strings.error_network);
                                        this.error_messages.push(this.strings.error_network);
                                    }
                                    this.saving_loader = false;

                                }).catch((e) => {
                                    Alertify.error(this.strings.error_network);
                                    this.error_messages.push(this.strings.error_network);
                                    this.saving_loader = false;
                                });
                            },
                            ()=>{ // ON CANCEL
                                this.saving_loader = false;
                                Alertify.warning(this.strings.cancel_action);
                            }).set({title: this.strings.save_warning_title})
                            .set({labels: {cancel: this.strings.confirm_cancel, ok: this.strings.confirm_ok}});
                    },

                    minify_query(weeks) {
                        var minify = [];
                        weeks.forEach(week => {
                            var wk = new Object();
                            wk.h = week.hours_dedications;
                            wk.s = week.weekstart;
                            wk.e = week.weekend;
                            wk.sections = [];
                            week.sections.forEach(section => {
                                var s = new Object();
                                s.sid = section.sectionid;
                                wk.sections.push(s);
                            });
                            minify.push(wk);
                        });
                        return JSON.stringify(minify);
                    },

                    empty_weeks() {
                        if (this.weeks.length >= 2 && this.weeks[0].sections.length < 1) {
                            return true;
                        }
                        for (let i = 0; i < this.weeks.length; i++) {
                            if (i > 0 && this.weeks[i].sections.length <= 0) {
                                return true;
                            }
                        }
                        return false;
                    },

                    weeks_deleted_from_course() {
                        for (var week_position = 0; week_position < this.weeks.length; week_position++) {
                            for (var section_position = 0; section_position < this.weeks[week_position].sections.length; section_position++) {
                                if (!this.section_exist(this.weeks[week_position].sections[section_position])) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    },

                    exists_mistakes() {
                        let exists_mistakes = this.error_messages.length > 0;
                        return exists_mistakes;
                    },

                    change_collapsabled(index) {
                        this.raw_weeks[index].collapsabled = !this.raw_weeks[index].collapsabled;
                    },

                    course_finished() {
                        let finished = false;
                        let last = this.weeks.length - 1;
                        let end = Moment(this.weeks[last].weekend).format("X");
                        let now = Moment().format("X");
                        if (now > end) {
                            finished = true;
                        } else {
                            finished = false;
                        }
                        return finished;
                    },

                    get_settings_status() {
                        let visible = true;
                        Object.keys(this.settings).map(key => {
                            if (!this.settings[key]) {
                                visible = false;
                            }
                        });
                        let status = visible ? this.strings.plugin_visible : this.strings.plugin_hidden;
                        return status;
                    },

                    get_help_content() {
                        var help_contents = [];
                        var help = new Object();
                        help.title = this.strings.title;
                        help.description = this.strings.description;
                        help_contents.push(help);
                        return help_contents;
                    }
                }
            });
        }

        function add_collapsabled_property_to_weeks(content) {
            for (var i = 0; i < content.weeks.length; i++) {
                var week = content.weeks[i];
                if (typeof (week.collapsabled) == "undefined") {
                    week.collapsabled = false;
                }
            }
            return content;
        }

        return {
            init: init
        };
    });