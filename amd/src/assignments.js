define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartcomponent",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
        "local_fliplearning/alertify",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, Chart, Pageheader, Emailform,Alertify) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', Chart);
            Vue.component('pageheader', Pageheader);
            Vue.component('emailform', Emailform);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#submissions",
                vuetify: new Vuetify(),
                data() {
                    return {
                        dialog : false,
                        selected_users : [],
                        moduleid : false,
                        valid_form: true,
                        subject: '',
                        subject_label: content.strings.email_strings.subject_label,
                        subject_rules: [
                            v => !!v || content.strings.email_strings.validation_subject_text,
                        ],
                        message: '',
                        message_label: content.strings.email_strings.message_label,
                        message_rules: [
                            v => !!v || content.strings.email_strings.validation_message_text,
                        ],
                        submit_button: content.strings.email_strings.submit_button,
                        cancel_button: content.strings.email_strings.cancel_button,
                        emailform_title: content.strings.email_strings.emailform_title,
                        sending_text: content.strings.email_strings.sending_text,
                        recipients: content.strings.email_strings.recipients_label,

                        loader_dialog: false,

                        snackbar: false,
                        snackbar_text: content.strings.email_strings.snackbar_text,
                        snackbar_close_text: content.strings.email_strings.snackbar_close,
                        snackbar_timeout: 3000,

                        strings : content.strings,
                        groups : content.groups,
                        userid : content.userid,
                        courseid : content.courseid,
                        timezone : content.timezone,
                        render_has : content.profile_render,
                        loading : false,
                        errors : [],
                        pages : content.pages,
                        submissions: content.submissions,
                        email_strings: content.strings.email_strings,
                    }
                },

                // watch: {
                //     loader_dialog (val) {
                //         if (!val) return
                //
                //         setTimeout(() => (this.loader_dialog = false, this.dialog = false, this.snackbar = true, this.$refs.form.reset()), 4000)
                //     },
                // },

                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#submissions").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    update_interactions(week){
                        this.loading = true;
                        let validresponse = false;
                        this.errors = [];
                        let data = {
                            action : "assignments",
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
                            validresponse = true;
                            this.submissions = response.data.submissions;
                        }).catch((e) => {
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    build_chart_submissions() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.assignsubs_chart_title,
                        };
                        chart.xAxis = {
                            categories: this.submissions.categories,
                            crosshair: true,
                        };
                        chart.yAxis = {
                            min: 0,
                            title: {
                                text: this.strings.assignsubs_chart_yaxis
                            }
                        };
                        chart.tooltip = {
                            valueSuffix: " estudiantes",
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                    point: {
                                    events: {
                                        click: function () {
                                            let serie_name = this.category.split('</b>');
                                            serie_name = serie_name[0] || '';
                                            serie_name = serie_name.split('<b>');
                                            serie_name = serie_name[1] || '';
                                            vue.subject = vue.email_strings.subject_prefix + " - " + serie_name;
                                            console.log(vue.subject);

                                            let x = this.x;
                                            let column = this.series.colorIndex;
                                            vue.dialog = true;
                                            vue.selected_users = vue.submissions.users[x][column];
                                            vue.moduleid = vue.submissions.modules[x];
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.submissions.data;
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    get_picture_url(userid){
                        let url = `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                        return url;
                    },

                    submit () {
                        let recipients = "";
                        this.selected_users.forEach(item => {
                            recipients=recipients.concat(item.id,",");
                        });
                        this.loader_dialog = true;
                        this.errors = [];
                        let data = {
                            action : "sendmail",
                            subject : this.subject,
                            recipients : recipients,
                            text : this.message,
                            userid : this.userid,
                            courseid : this.courseid,
                            moduleid : this.moduleid,
                        };
                        console.log(data);
                        Axios({
                            method:'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params : data,
                        }).then((response) => {
                            console.log(response);
                            this.loader_dialog = false;
                            this.dialog = false;
                            this.snackbar = true;
                            this.$refs.form.reset();
                            Alertify.success(this.snackbar_text);
                        }).catch((e) => {
                            console.log(e);
                            // this.errors.push(this.strings.api_error_network);
                            Alertify.error('Error en la comunicacion con el servidor');
                            this.loader_dialog = false;
                            // this.dialog = false;
                            // this.$refs.form.reset();
                        });
                    },


                    reset () {
                        this.dialog = false;
                    },

                    update_dialog (value) {
                        this.dialog = value;
                    }
                }
            })
        }

        return {
            init : init
        };
    });