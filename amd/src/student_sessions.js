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
            console.log(content);
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
                    }
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#work_sessions").style.display = "block";
                },
                methods : {
                    get_help_content(){
                        return '';
                    },

                    update_interactions(week){
                        this.loading = true;
                        // this.errors = [];
                        // let data = {
                        //     action : "worksessions",
                        //     userid : this.userid,
                        //     courseid : this.courseid,
                        //     weekcode : week.weekcode,
                        //     profile : this.render_has,
                        // }
                        // Axios({
                        //     method:'get',
                        //     url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                        //     params : data,
                        // }).then((response) => {
                        //     if (response.status == 200 && response.data.ok) {
                        //         this.hours_sessions = response.data.data.sessions_by_hours;
                        //         this.session_count = response.data.data.session_count;
                        //     } else {
                        //         this.error_messages.push(this.strings.error_network);
                        //     }
                        // }).catch((e) => {
                        //     this.errors.push(this.strings.api_error_network);
                        // }).finally(() => {
                        //     this.loading = false;
                        // });
                        // return this.data;
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