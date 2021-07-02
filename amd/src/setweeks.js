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
    function(Vue, Vuetify, Axios, Sortable, Draggable, Datepicker, Moment, Alertify, PageHeader) {
        "use strict";

        function init(content) {
            // content = add_collapsabled_property_to_weeks(content);
            console.log(content);
            Vue.use(Vuetify);
            Vue.component('draggable', Draggable);
            Vue.component('datepicker', Datepicker);
            Vue.component('pageheader', PageHeader);
            const app = new Vue({
                delimiters: ["[[", "]]"],
                el: "#setweeks",
                vuetify: new Vuetify(),
                data: {
                    display_settings: false,
                    settings: content.settings,
                    new_group: false,
                    scroll_mode: false,
                    // weeks_started_at: new Date(Moment(Number(content.weeks[0].weekstart) * 1000)),
                    // weeks_started_at: new Date(Moment(content.weeks[0].weekstart)),
                    // weeks_started_at: Number(content.weeks[0].weekstart) * 1000,
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
                    save_successful: false,

                    dates: [],
                    datesLabels: [],
                    modal: false,
                    modalDatePicker: false,

                    // raw_weeks: [
                    //     {
                    //         dates: [],
                    //         modal: false,
                    //         blockDate: null,
                    //     },
                    // ],
                },
                mounted() {
                    this.initBlockDates();
                    document.querySelector("#setweeks-loader").style.display = "none";
                    document.querySelector("#setweeks").style.display = "block";
                },
                computed: {
                    // weeks() {
                    //     for (let i = 0; i < this.raw_weeks.length; i++) {
                    //         let week = this.raw_weeks[i];
                    //         if (i == 0) {
                    //             let start_weeks = this.weeks_started_at;
                    //             week.weekstart = start_weeks;
                    //             week.weekend = this.get_end_week(this.weeks_started_at);
                    //         } else {
                    //             week.weekstart = this.get_start_week(this.raw_weeks[i - 1].weekend);
                    //             week.weekend = this.get_end_week(week.weekstart);
                    //         }
                    //     }
                    //     return this.raw_weeks;
                    // },

                    // dateRangeText () {
                    //     let dates_array = this.dates.map(date => new Date(date));
                    //     dates_array.sort(this.sortDates);
                    //     this.datesLabels = dates_array.map(date => this.formatDate(date));
                    //     return this.datesLabels.join(' → ');
                    // },

                    dateRangeText() {
                        return (i) => {
                            // this.min_date = this.raw_weeks[i].dates[0];
                            let dates_array = this.raw_weeks[i].dates.map((date) => new Date(date));
                            // Se ordenan las fechas seleccionadas
                            dates_array.sort(this.sortDates);

                            dates_array = dates_array.map(date => this.formatDate(date));
                            this.raw_weeks[i].weekstart = dates_array[0].split('/').reverse().join('-');
                            this.raw_weeks[i].weekend = dates_array[1]
                                ? dates_array[1].split('/').reverse().join('-')
                                : this.raw_weeks[i].weekstart;

                            // Se retorna el arreglo de fechas ordenado y formateado
                            //return dates_array.map((date) => this.formatDate(date)).join(" → ");
                            return dates_array.join(" → ");
                            // return this.raw_weeks[i].dates.join(" → ");
                        };
                    },
                },
                methods: {
                    section_name(section) {
                        let name = null;
                        if (typeof(section.section_name) != 'undefined' && section.section_name.length > 0) {
                            name = section.section_name;
                        } else {
                            name = section.name;
                        }
                        return name;
                    },

                    section_exist(section) {
                        let exist = true;
                        if (typeof(section) != 'undefined' && typeof(section.exists) != 'undefined' && section.exists == false) {
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

                    add_week () {
                        let start = this.get_start_week(new Date(this.get_last_date()));
                        let end = this.get_end_week(new Date(start), 6);
                        this.raw_weeks.push({
                            name: this.strings.week,
                            position: (this.raw_weeks.length + 1),
                            weekstart: start,
                            weekend: end,
                            collapsabled: false,
                            hours_dedications: 0,
                            removable: true,
                            sections: [],

                            dates: [start, end],
                            modal: false,
                            blockDate: start,
                        });
                    },

                    // add_week() {
                    //     let start = this.get_start_week(new Date(this.get_last_date()));
                    //     let end = this.get_end_week(new Date(start), 7);
                    //
                    //     // Se agrega una nueva semana al arreglo de semanas
                    //     this.raw_weeks.push({
                    //         dates: [start, end],
                    //         modal: false,
                    //         blockDate: start,
                    //     });
                    // },

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

                    // get_start_week(pass_week) {
                    //     let start_date = Moment(Moment(pass_week).add(1, 'days')).format('YYYY-MM-DD');
                    //     return start_date;
                    // },
                    //
                    // get_end_week(start_week) {
                    //     let end_date = Moment(Moment(start_week).add(6, 'days')).format('YYYY-MM-DD');
                    //     return end_date;
                    // },

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
                            if (typeof(date) != 'number') {
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
                        console.log(this.raw_weeks);
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
                                () => {
                                    this.saving_loader = true;
                                    let weeks = this.raw_weeks;
                                    // weeks[0].weekstart = Moment(weeks[0].weekstart).format("YYYY-MM-DD");
                                    let data = {
                                        action: "saveconfigweek",
                                        userid: this.userid,
                                        courseid: this.courseid,
                                        newinstance: this.new_group,
                                        weeks: this.minify_query(weeks), // Stringify is a hack to clone object :D
                                        url: window.location.href,
                                    };
                                    Axios({
                                        method: 'get',
                                        url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                                        params: data,
                                    }).then((response) => {
                                        if (response.status == 200 && response.data.ok) {
                                            console.log(response.data.data.settings);
                                            this.settings = response.data.data.settings;
                                            Alertify.success(this.strings.save_successful);
                                            this.save_successful = true;
                                        } else {
                                            Alertify.error(this.strings.error_network);
                                            this.error_messages.push(this.strings.error_network);
                                        }
                                    }).catch((e) => {
                                        Alertify.error(this.strings.error_network);
                                        this.error_messages.push(this.strings.error_network);
                                    }).finally(() => {
                                        this.saving_loader = false;
                                    });
                                },
                                () => { // ON CANCEL
                                    this.saving_loader = false;
                                    Alertify.warning(this.strings.cancel_action);
                                }).set({ title: this.strings.save_warning_title })
                            .set({ labels: { cancel: this.strings.confirm_cancel, ok: this.strings.confirm_ok } });
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
                        if (this.raw_weeks.length >= 2 && this.raw_weeks[0].sections.length < 1) {
                            return true;
                        }
                        for (let i = 0; i < this.raw_weeks.length; i++) {
                            if (i > 0 && this.raw_weeks[i].sections.length <= 0) {
                                return true;
                            }
                        }
                        return false;
                    },

                    weeks_deleted_from_course() {
                        for (let week_position = 0; week_position < this.raw_weeks.length; week_position++) {
                            for (let section_position = 0; section_position < this.raw_weeks[week_position].sections.length; section_position++) {
                                if (!this.section_exist(this.raw_weeks[week_position].sections[section_position])) {
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
                        let last = this.raw_weeks.length - 1;
                        let end = Moment(this.raw_weeks[last].dates[1]).format("X");
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
                        let status = visible ? this.strings.tw_plugin_visible : this.strings.tw_plugin_hidden;
                        return status;
                    },

                    sortDates (a, b) {
                        return b.getTime() > a.getTime() ? -1 : b.getTime() < a.getTime() ? 1 : 0;
                    },

                    formatDate (date) {
                        const year = date.getUTCFullYear();
                        const month_number = date.getUTCMonth() + 1;
                        const month = month_number < 10 ? `0${month_number}` : month_number;
                        const day_number = date.getUTCDate();
                        const day = day_number < 10 ? `0${day_number}` : day_number;
                        return `${day}/${month}/${year}`;
                    },

                    get_help_content() {
                        let help_contents = [];
                        let help = new Object();
                        help.title = this.strings.help_title;
                        help.description = this.strings.help_description;
                        help_contents.push(help);
                        return help_contents;
                    },

                    get_start_week(pass_week) {
                        // Se agrega un día a partir de la fecha final del rango anterior
                        let start_weeks = new Date(pass_week.setDate(pass_week.getDate() + 1));
                        // Convertimos la fecha a un formato permitido por el datepicker
                        return start_weeks.toISOString().substr(0, 10);
                    },

                    get_end_week(start_week, days) {
                        // Establecemos la fecha final (una semana a partir de la fecha inicial)
                        let end_date = new Date(start_week.setDate(start_week.getDate() + days));
                        // Convertimos la fecha a un formato permitido por el datepicker
                        return end_date.toISOString().substr(0, 10);
                    },

                    get_last_date() {
                        return this.raw_weeks[this.raw_weeks.length - 1].dates[
                            this.raw_weeks[this.raw_weeks.length - 1].dates.length === 1 ? 0 : 1
                        ];
                    },

                    saveWeek(index) {
                        // Cerrar el modal
                        this.raw_weeks[index].modal = false;

                        // Recalcular las siguientes fechas con un rango de siete días
                        for (let i = index + 1; i < this.raw_weeks.length; i++) {

                            let dates_array = this.raw_weeks[i].dates.map((date) => new Date(date));
                            let diffDays = 0;

                            if(dates_array.length > 1){
                                const diffTime = Math.abs(dates_array[0] - dates_array[1]);
                                diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            }

                            // Ordenamos las fechas de la semana seleccionada
                            let dates_aux = this.raw_weeks[i-1].dates.map((date) => new Date(date));
                            dates_aux.sort(this.sortDates);
                            let new_last_dates = dates_aux.map((date) => date.toISOString().substr(0, 10));

                            let week = this.raw_weeks[i];
                            let newLastDate = new_last_dates[ new_last_dates.length === 1 ? 0 : 1 ];
                            let start = this.get_start_week(new Date(newLastDate));
                            let end = this.get_end_week(new Date(start), diffDays);
                            week.dates = [start, end];

                            // Actualizar blockDate
                            //let ends = this.get_end_week(new Date(this.raw_weeks[i - 1].weekend), 1);
                            this.raw_weeks[i].blockDate = this.get_end_week(new Date(this.raw_weeks[i - 1].weekend), 1);
                            // console.log(this.raw_weeks[i]);
                        }
                    },

                    initBlockDates() {
                        if (this.raw_weeks.length > 1) {
                            for (let i = 1; i < this.raw_weeks.length; i++) {
                                this.raw_weeks[i].blockDate = this.get_end_week(new Date(this.raw_weeks[i - 1].weekend), 1);
                            }
                        }
                        // console.log(this.raw_weeks);
                    },
                }
            });
        }

        // function add_collapsabled_property_to_weeks(content) {
        //     for (let i = 0; i < content.weeks.length; i++) {
        //         let week = content.weeks[i];
        //         if (typeof(week.collapsabled) == "undefined") {
        //             week.collapsabled = false;
        //         }
        //     }
        //     return content;
        // }

        return {
            init: init
        };
    });