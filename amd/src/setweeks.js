define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/sortablejs",
        "local_fliplearning/draggable",
        "local_fliplearning/moment",
        "local_fliplearning/alertify",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Sortable, Draggable, Moment, Alertify, PageHeader) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('draggable', Draggable);
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
                    strings: content.strings,
                    sections: content.sections,
                    sectionsWithCms: content.sectionsWithCms,
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
                    previousRangeStart: null,
                },
                beforeMount() {
                    if (this.raw_weeks.length) {
                        this.setModulesInSections();
                    }
                },
                mounted() {
                    this.initBlockDates();
                    document.querySelector("#setweeks-loader").style.display = "none";
                    document.querySelector("#setweeks").style.display = "block";
                },
                computed: {

                    dateRangeText() {
                        return (i) => {
                            let dates_array = this.raw_weeks[i].dates.map((date) => new Date(date));
                            // Se ordenan las fechas seleccionadas
                            dates_array.sort(this.sortDates);

                            dates_array = dates_array.map(date => this.formatDate(date));
                            this.raw_weeks[i].weekstart = dates_array[0].split('/').reverse().join('-');
                            this.raw_weeks[i].weekend = dates_array[1]
                                ? dates_array[1].split('/').reverse().join('-')
                                : this.raw_weeks[i].weekstart;

                            // Se retorna el arreglo de fechas ordenado y formateado
                            // return dates_array.map((date) => this.formatDate(date)).join(" → ");
                            return dates_array.join(" → ");
                            // Return this.raw_weeks[i].dates.join(" → ");
                        };
                    },

                    totalTime() {
                        return (i, j) => {
                            let total = 0;
                            this.raw_weeks[i].sections[j].cms.forEach((a) => {
                                total = total + this.isNumber(a.hoursDedication);
                            });
                            return total;
                        };
                    },
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

                    addWeek() {
                        let weeksCount = this.raw_weeks.length;
                        let endLastWeek = this.raw_weeks[weeksCount - 1].dates[1];

                        // let start = this.get_start_week(new Date(this.get_last_date()));
                        let start = this.addDays(new Date(endLastWeek), 1);
                        let end = this.addDays(new Date(start), 6);
                        this.raw_weeks.push({
                            name: this.strings.week,
                            position: (this.raw_weeks.length + 1),
                            weekstart: start,
                            weekend: end,
                            collapsabled: false,
                            hours_dedications: 0,
                            removable: true,
                            sections: [],
                            cmsTimeDialog: false,

                            dates: [start, end],
                            modal: false,
                            blockDate: start,
                        });
                    },

                    remove_week(week, index) {
                        if (index == 0) {
                            return null;
                        }
                        for (let i = 0; i < week.sections.length; i++) {
                            this.sections.push(week.sections[i]);
                        }
                        let element_index = this.raw_weeks.indexOf(week);
                        this.raw_weeks.splice(element_index, 1);
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
                                () => {
                                    this.saving_loader = true;
                                    let weeks = this.raw_weeks;
                                    // Weeks[0].weekstart = Moment(weeks[0].weekstart).format("YYYY-MM-DD");
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

                    sortDates(a, b) {
                        return b.getTime() > a.getTime() ? -1 : b.getTime() < a.getTime() ? 1 : 0;
                    },

                    formatDate(date) {
                        const year = date.getUTCFullYear();
                        const month_number = date.getUTCMonth() + 1;
                        const month = month_number < 10 ? `0${month_number}` : month_number;
                        const day_number = date.getUTCDate();
                        const day = day_number < 10 ? `0${day_number}` : day_number;
                        return `${day}/${month}/${year}`;
                    },

                    saveWeek(dates, index) {

                        // Guardar la selección de una semana completa
                        let newVal = dates[dates.length == 1 ? 0 : 1];
                        let nextDate = this.addDays(new Date(newVal), 6);
                        this.raw_weeks[index].dates = [newVal, nextDate];

                        // Cerrar el modal
                        this.raw_weeks[index].modal = false;

                        // Convertir a dates la fecha de inicio anterior del rango
                        // y la fecha actual de inicio del rango
                        let previousStartDate = new Date(this.previousRangeStart);
                        let currentStartDate = new Date(this.raw_weeks[index].dates[0]);

                        // Obtener diferencia en dias entre el inicio del rango previo
                        // y el inicio de rango actual
                        let diffTime = currentStartDate - previousStartDate;
                        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        // Recalcular las fechas siguientes a la semana
                        // que se modificó
                        let i, currentDates, start, end;
                        for (i = index + 1; i < this.raw_weeks.length; i++) {

                            // Ordenar las fechas de la semana seleccionada
                            currentDates = this.raw_weeks[i].dates.map(
                                (date) => new Date(date)
                            );

                            // Calcular el nuevo inicio y final del rango de fechas
                            // sumando la diferencia de dias antes calculado
                            start = this.addDays(currentDates[0], diffDays);
                            end = this.addDays(currentDates[1], diffDays);

                            // Asignar nuevo rango
                            this.raw_weeks[i].dates = [start, end];

                            // Asignar nuevas fechas de bloqueo de rango
                            this.raw_weeks[i].blockDate = this.addDays(new Date(this.raw_weeks[i - 1].dates[1]), 1)
                        }
                    },

                    addDays(date, days) {
                        let nextDate = new Date(date.setDate(date.getDate() + days));
                        return nextDate.toISOString().substr(0, 10);
                    },

                    savePreviousRangeStart(index) {
                        this.previousRangeStart = this.raw_weeks[index].dates[0];
                    },

                    get_help_content() {
                        let help_contents = [];
                        let help = new Object();
                        help.title = this.strings.help_title;
                        help.description = this.strings.help_description;
                        help_contents.push(help);
                        return help_contents;
                    },

                    initBlockDates() {
                        if (this.raw_weeks.length > 1) {
                            for (let i = 1; i < this.raw_weeks.length; i++) {
                                this.raw_weeks[i].blockDate = this.addDays(new Date(this.raw_weeks[i - 1].weekend), 1);
                            }
                        }
                    },

                    setModulesInSections() {
                        let i, j, sid;
                        for (i = 0; i < this.raw_weeks.length; i++) {
                            this.raw_weeks[i].cmsTimeDialog = false;
                            for (j = 0; j < this.raw_weeks[i].sections.length; j++) {
                                sid = `sid${this.raw_weeks[i].sections[j].sectionid}`;
                                this.raw_weeks[i].sections[j].cms = this.sectionsWithCms[sid];
                            }
                        }

                        for (i = 0; i < this.sections.length; i++) {
                            sid = `sid${this.sections[i].sectionid}`;
                            this.sections[i].cms = this.sectionsWithCms[sid];
                        }
                    },

                    isNumber(x) {
                        if (x === "") {
                            return 0;
                        }
                        if (isNaN(x)) {
                            return 0;
                        }
                        return parseInt(x);
                    },

                    getModuleIcon(modname) {
                        return `${M.cfg.wwwroot}/theme/image.php/boost/${modname}/1/icon`;
                    },

                    getModuleUrl(module) {
                        return `${M.cfg.wwwroot}/mod/${module.modname}/view.php?id=${module.id}`;
                    },

                    saveCourseModulesTime(dialog, weekIndex) {
                        let total = 0;
                        this.raw_weeks[weekIndex].sections.forEach((section) => {
                            section.cms.forEach((cm) => {
                                total = total + this.isNumber(cm.hoursDedication);
                            });
                        });
                        this.raw_weeks[weekIndex].hours_dedications = this.isNumber(total);
                        dialog.value = false;
                    },
                }
            });
        }

        return {
            init: init
        };
    });