define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartcomponent",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, Chart, Pageheader) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', Chart);
            Vue.component('pageheader', Pageheader);
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
                        default_quiz: null,
                        attempts_categories: [],
                        attempts_series: [],
                        hardest_categories: [],
                        hardest_series: [],
                    }
                },
                beforeMount(){
                    if (this.quiz.data.length) {
                        this.default_quiz = this.quiz.data[0].attempts;
                        this.calculate_questions_attempts(this.default_quiz);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#quiz").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    get_quiz_info_text1() {
                        let questions_number = this.default_quiz.details.questions;
                        let questions_label = this.strings.questions_text;
                        if (questions_number == 1) {
                            questions_label = this.strings.question_text;
                        }
                        let text = `* ${this.strings.quiz_info_text} ${questions_number} ${questions_label}`;
                        return text;
                    },

                    get_quiz_info_text2() {
                        let attempts_number = this.default_quiz.details.attempts;
                        let attempts_label = this.strings.attempts_text;
                        if (attempts_number == 1) {
                            attempts_label = this.strings.attempt_text;
                        }
                        let students_number = this.default_quiz.details.users;
                        let students_label = this.strings.students_text;
                        if (students_number == 1) {
                            students_label = this.strings.student_text;
                        }
                        let text = `* ${attempts_number} ${attempts_label} ${students_number} ${students_label}`;
                        return text;
                    },

                    build_chart_questions_attempts() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: 'Intentos de Preguntas'
                        };
                        chart.subtitle = {
                            text: 'Subtitulo',
                        };
                        chart.xAxis = {
                            categories: this.attempts_categories
                        };
                        chart.yAxis = [{
                            min: 0,
                            allowDecimals: false,
                            title: {
                                text: 'Número de Intentos'
                            }
                        }];
                        chart.plotOptions = {
                            column: {
                                stacking: 'normal'
                            }
                        };
                        chart.series = this.attempts_series;
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    build_chart_hardest_questions() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: 'Preguntas mas dificiles',
                        };
                        chart.subtitle = {
                            text: 'subtitulo',
                        };
                        chart.xAxis = {
                            categories: this.hardest_categories,
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.yAxis = [{
                            min: 0,
                            allowDecimals: false,
                            title: {
                                text: 'Intentos incorrectos'
                            }
                        }];
                        chart.series = this.hardest_series;
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    calculate_questions_attempts(quiz) {
                        console.log(quiz);
                        let questions = quiz.questions;
                        let attempts_categories = [];
                        let cont = 1;

                        let correct = [],
                            partcorrect = [],
                            incorr = [],
                            gaveup = [],
                            needgrade = [],
                            hardest = [];
                        let co, pc, ic, ga, ng, ha = 0;

                        questions.forEach(question => {
                            co, pc, ic, ga, ng = 0;
                            co = (question.gradedright || 0) + (question.mangrright || 0);
                            pc = (question.gradedpartial || 0) + (question.mangrpartial || 0);
                            ic = (question.gradedwrong || 0) + (question.mangrwrong || 0);
                            ga = (question.gaveup || 0) + (question.mangaveup || 0);
                            ng = (question.needsgrading || 0) + (question.mangaveup || 0) +
                                (question.finished || 0) + (question.manfinished || 0);

                            // console.log({ co, pc, ic, ga, ng });
                            correct.push(co);
                            partcorrect.push(pc);
                            incorr.push(ic);
                            gaveup.push(ga);
                            needgrade.push(ng);

                            ha = pc + ic + ga;
                            hardest.push({
                                qu: `P${cont}`,
                                in: ha
                            });

                            // console.log(question);
                            attempts_categories.push(`P${cont++}`);
                        });

                        let attempts_series = [];
                        attempts_series.push({
                            name: 'Correctas',
                            data: correct
                        });
                        attempts_series.push({
                            name: 'Parcialmente Correctas',
                            data: partcorrect
                        });
                        attempts_series.push({
                            name: 'Incorrectas',
                            data: incorr
                        });
                        attempts_series.push({
                            name: 'No intentadas',
                            data: gaveup
                        });
                        attempts_series.push({
                            name: 'Sin calificar',
                            data: needgrade
                        });

                        // console.log(attempts_categories);
                        // console.log(attempts_series);
                        hardest.sort(this.compare_hardest);
                        // console.log(hardest);


                        let hardest_categories = [],
                            hardest_data = [];
                        hardest.forEach(element => {
                            if (element.in) {
                                hardest_categories.push(element.qu);
                                hardest_data.push(element.in);
                            }
                        });

                        let hardest_series = [{
                            name: "Intentos Incorrectos",
                            data: hardest_data
                        }];

                        this.attempts_categories = attempts_categories;
                        this.attempts_series = attempts_series;
                        this.hardest_categories = hardest_categories;
                        this.hardest_series = hardest_series;
                    },

                    compare_hardest(a, b) {
                        if (a.in > b.in) {
                            return -1;
                        }
                        if (a.in < b.in) {
                            return 1;
                        }
                        return 0;
                    },

                    change_quiz(value) {
                        console.log(value);
                    },

                    update_interactions(week){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "time",
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
                                // this.inverted_time = response.data.data.inverted_time;
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

                }
            })
        }

        return {
            init : init
        };
    });