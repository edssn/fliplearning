define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, Pageheader) {
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
                    }
                },
                beforeMount(){
                    if (this.dropout.clusters.length) {
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

                    change_cluster(users) {
                        let selected_users = [];
                        this.dropout.users.forEach(user => {
                            if (users.includes(user.id)) {
                                selected_users.push(user);
                            }
                        });
                        this.cluster_users = selected_users;
                        this.selected_user = this.cluster_users[0] || {};
                        console.log(this.cluster_users);
                        console.log(this.selected_user);
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
                                this.inverted_time = response.data.data.inverted_time;
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