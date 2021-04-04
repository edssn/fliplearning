define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, PageHeader, HelpDialog) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', PageHeader);
            Vue.component('helpdialog', HelpDialog);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#quiz",
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

                        quiz : content.quiz,
                        questions_attempts_colors: content.questions_attempts_colors,
                        hardest_questions_colors: content.hardest_questions_colors,
                        default_quiz: null,
                        attempts_categories: [],
                        attempts_series: [],
                        attempts_questions: [],
                        hardest_categories: [],
                        hardest_series: [],
                        hardest_questions: [],

                        help_dialog: false,
                        help_contents: [],
                    }
                },
                beforeMount(){
                    if (this.quiz.length) {
                        this.default_quiz = this.quiz[0].attempts;
                        this.calculate_questions_attempts(this.default_quiz);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#quiz").style.display = "block";
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

                    get_quiz_info_text1() {
                        let text = '';
                        if (this.default_quiz) {
                            let questions_number = this.default_quiz.details.questions;
                            let questions_label = this.strings.questions_text;
                            if (questions_number == 1) {
                                questions_label = this.strings.question_text;
                            }
                            text = `* ${this.strings.quiz_info_text} ${questions_number} ${questions_label}`;
                        }
                        return text;
                    },

                    get_quiz_info_text2() {
                        let text = '';
                        if (this.default_quiz) {
                            let attempts_number = this.default_quiz.details.attempts;
                            let doing_text = this.strings.doing_text_plural;
                            if (attempts_number == 1) {
                                doing_text = this.strings.doing_text_singular;
                            }
                            let students_number = this.default_quiz.details.users;
                            let students_label = this.strings.students_text;
                            if (students_number == 1) {
                                students_label = this.strings.student_text;
                            }
                            text = `* ${attempts_number} ${doing_text} ${students_number} ${students_label}`;
                        }
                        return text;
                    },

                    build_questions_attempts_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {
                            text: null
                        };
                        chart.colors = this.questions_attempts_colors;
                        chart.xAxis = {
                            categories: this.attempts_categories
                        };
                        chart.yAxis = [{
                            min: 0,
                            allowDecimals: false,
                            title: {
                                text: this.strings.questions_attempts_yaxis_title
                            }
                        }];
                        chart.tooltip = {
                            useHTML:true,
                            formatter: function() {
                                let question_name = this.x;
                                let attemps = this.y;
                                let total_attemps = this.total;
                                let percentage = Math.round(Number(this.percentage));
                                let series_name = this.series.name;
                                let of_conector = vue.strings.of_conector;
                                let review_question = vue.strings.review_question;
                                return '<small>' + question_name + '</small></br>'
                                    + '<b style="color: ' + this.color + ';">• </b>'
                                    + series_name + ': ' + attemps + ' ' + of_conector + ' ' +total_attemps
                                    + ' (' + percentage + '%)' + '<br/>'
                                    + '<small>' + review_question + '</small>';
                            }
                        };
                        chart.plotOptions = {
                            column: {
                                stacking: 'normal'
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            let question = vue.attempts_questions[this.x];
                                            let id = question.id;
                                            let url = M.cfg.wwwroot + '/question/preview.php?id='+id+'&courseid='+vue.courseid;
                                            window.open(url, '_blank', 'top=50,left=50,width=900,height=600');
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.attempts_series;
                        return chart;
                    },

                    build_hardest_questions_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: null,
                            style: {fontFamily: 'poppins'},
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.hardest_questions_colors;
                        chart.xAxis = {
                            categories: this.hardest_categories,
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.tooltip = {
                            useHTML:true,
                            formatter: function() {
                                let position = this.point.x;
                                let question_info = vue.hardest_questions[position];
                                let question_name = this.x;
                                let serie_name = this.series.name;
                                let value = this.y;
                                let of_conector = vue.strings.of_conector;
                                let review_question = vue.strings.review_question;
                                return '<small>' + question_name + '</small></br>'
                                    + '<b style="color: ' + this.color + ';">• </b>'
                                    + serie_name + ': ' + question_info.ha + ' ' + of_conector + ' '
                                    + question_info.to + ' (' + value + '%)' + '<br/>'
                                    + '<small>' + review_question + '</small>';
                            }
                        };
                        chart.yAxis = [{
                            min: 0,
                            allowDecimals: false,
                            title: {
                                text: this.strings.hardest_questions_yaxis_title
                            },
                            labels: {
                                format: '{value} %',
                            },
                        }];
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                    point: {
                                    events: {
                                        click: function () {
                                            let question = vue.hardest_questions[this.x];
                                            let id = question.id;
                                            let url = M.cfg.wwwroot + '/question/preview.php?id='+id+'&courseid='+vue.courseid;
                                            window.open( url, '_blank', 'top=50,left=50,width=900,height=600');
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.hardest_series;
                        return chart;
                    },

                    calculate_questions_attempts(quiz) {
                        let questions = quiz.questions;
                        let attempts_categories = [];
                        let cont = 1, ql = "";

                        let correct = [], partcorrect = [], incorr = [], gaveup = [], needgrade = [], hardest = [],
                            attempts_questions = [];
                        let co, pc, ic, ga, ng, to, ha = 0;

                        questions.forEach(question => {
                            co, pc, ic, ga, ng, to = 0;
                            co = (question.gradedright || 0) + (question.mangrright || 0);
                            pc = (question.gradedpartial || 0) + (question.mangrpartial || 0);
                            ic = (question.gradedwrong || 0) + (question.mangrwrong || 0);
                            ga = (question.gaveup || 0) + (question.mangaveup || 0);
                            ng = (question.needsgrading || 0) + (question.mangaveup || 0) +
                                (question.finished || 0) + (question.manfinished || 0);

                            correct.push(co);
                            partcorrect.push(pc);
                            incorr.push(ic);
                            gaveup.push(ga);
                            needgrade.push(ng);

                            ql = 'P' + cont;
                            ha = pc + ic + ga;
                            to = co + pc + ic + ga + ng;
                            hardest.push({ id: question.id, qu: ql, ha: ha, to: to, pe: Math.round((ha * 100) / to), });
                            attempts_categories.push(ql);
                            attempts_questions.push(question);
                            cont++;
                        });

                        let attempts_series = [];
                        attempts_series.push({
                            name: this.strings.correct_attempt,
                            data: correct
                        });
                        attempts_series.push({
                            name: this.strings.partcorrect_attempt,
                            data: partcorrect
                        });
                        attempts_series.push({
                            name: this.strings.incorrect_attempt,
                            data: incorr
                        });
                        attempts_series.push({
                            name: this.strings.blank_attempt,
                            data: gaveup
                        });
                        attempts_series.push({
                            name: this.strings.needgraded_attempt,
                            data: needgrade
                        });

                        let hardest_categories = [], hardest_data = [], hardest_questions = [];
                        hardest.sort(this.compare_hardest);
                        hardest.forEach(element => {
                            if (element.pe) {
                                hardest_categories.push(element.qu);
                                hardest_data.push(element.pe);
                                hardest_questions.push(element);
                            }
                        });

                        let hardest_series = [{
                            name: this.strings.hardest_questions_yaxis_title,
                            data: hardest_data
                        }];

                        this.attempts_categories = attempts_categories;
                        this.attempts_series = attempts_series;
                        this.attempts_questions = attempts_questions;
                        this.hardest_categories = hardest_categories;
                        this.hardest_series = hardest_series;
                        this.hardest_questions = hardest_questions;
                    },

                    update_interactions(week){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "quiz",
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
                                this.quiz = response.data.data.quiz;
                                if (this.quiz.length) {
                                    this.default_quiz = this.quiz[0].attempts;
                                    this.calculate_questions_attempts(this.default_quiz);
                                } else {
                                    this.reset_graphs();
                                };
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

                    reset_graphs () {
                        this.default_quiz = null;
                        this.attempts_categories = [];
                        this.attempts_series = [];
                        this.attempts_questions = [];
                        this.hardest_categories = [];
                        this.hardest_series = [];
                        this.hardest_questions = [];
                    },

                    compare_hardest(a, b) {
                        if (a.pe > b.pe) {
                            return -1;
                        }
                        if (a.pe < b.pe) {
                            return 1;
                        }
                        return 0;
                    },

                    open_chart_help(chart) {
                        let contents = [];
                        if (chart == "questions_attempts") {
                            contents.push({
                                title: this.strings.questions_attempts_help_title,
                                description: this.strings.questions_attempts_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.questions_attempts_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.questions_attempts_help_description_p3,
                            });
                        } else if (chart == "hardest_questions") {
                            contents.push({
                                title: this.strings.hardest_questions_help_title,
                                description: this.strings.hardest_questions_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.hardest_questions_help_description_p2,
                            });
                            contents.push({
                                description: this.strings.hardest_questions_help_description_p3,
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