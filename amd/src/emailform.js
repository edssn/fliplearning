define([
    "local_fliplearning/axios",
    "local_fliplearning/alertify",
    ],
    function (Axios, Alertify){
        const emailform = {
        template:`
                <v-main mt-10>
                    <v-row>
                        <v-col sm="12">
                            <v-dialog
                                v-model="dialog"
                                width="800"
                                @click:outside="closeDialog()"
                                @keydown.esc="closeDialog()"
                            >
                                <v-card>
                                    <v-toolbar color="#118AB2" dark>
                                        <span v-text="emailform_title"></span>
                                        <v-spacer></v-spacer>
                                        <v-btn icon @click="reset">
                                            <v-icon v-text="close_icon"></v-icon>
                                        </v-btn>
                                    </v-toolbar>
        
                                    <v-container>
                                        <v-row>
                                            <v-col cols="12" sm="12">
    
                                                <v-chip class="ma-2" color="#118AB2" label dark>
                                                    <span v-text="recipients"></span>
                                                </v-chip>
    
                                                <template v-for="(user, index, key) in selected_users">
                                                    <v-chip class="ma-2">
                                                        <v-avatar left>
                                                            <img :src="get_picture_url(user.id)">
                                                        </v-avatar>
                                                        <span>{{user.firstname}} {{user.lastname}}</span>
                                                    </v-chip>
                                                </template>
    
                                            </v-col>
                                        </v-row>
    
                                        <v-row>
                                            <v-col cols="12" sm="12">
                                                <v-form ref="form" v-model="valid_form">
                                                    <v-text-field
                                                            v-model="strings.subject"
                                                            :label="subject_label"
                                                            :rules="subject_rules"
                                                            required
                                                            solo
                                                    ></v-text-field>
    
                                                    <v-textarea
                                                            v-model="message"
                                                            :label="message_label"
                                                            :rules="message_rules"
                                                            required
                                                            solo
                                                    ></v-textarea>
    
                                                    <v-btn @click="submit" :disabled="!valid_form">
                                                        <span v-text="submit_button"></span>
                                                    </v-btn>
    
                                                    <v-btn @click="reset">
                                                        <span v-text="cancel_button"></span>
                                                    </v-btn>
    
                                                    <v-spacer></v-spacer>
    
                                                </v-form>
                                            </v-col>
                                        </v-row>
                                    </v-container>
        
                                </v-card>
                            </v-dialog>
                        </v-col>
                    </v-row>
                    
                    <v-row>
                        <v-col sm="12">
                            <div class="text-center">
                                <v-dialog
                                        v-model="loader_dialog"
                                        persistent
                                        width="300"
                                >
                                    <v-card color="#118AB2" dark>
                                        <v-card-text>
                                            <span v-text="sending_text"></span>
                                            <v-progress-linear
                                                    indeterminate
                                                    color="white"
                                                    class="mb-0"
                                            ></v-progress-linear>
                                        </v-card-text>
                                    </v-card>
                                </v-dialog>
                            </div>
                        </v-col>
                    </v-row>
               </v-main>
                `,
        props:[
            'dialog',
            'selected_users',
            'strings',
            'moduleid',
            'modulename',
            'courseid',
            'userid',
            'pluginsection',
            'component',
            'target'
        ],
        data(){
            return {
                close_icon: 'mdi-minus',
                valid_form: true,
                subject_label: this.strings.subject_label,
                subject_rules: [
                    v => !!v || this.strings.validation_subject_text,
                ],
                message: '',
                message_label: this.strings.message_label,
                message_rules: [
                    v => !!v || this.strings.validation_message_text,
                ],
                submit_button: this.strings.submit_button,
                cancel_button: this.strings.cancel_button,
                emailform_title: this.strings.emailform_title,
                sending_text: this.strings.sending_text,
                recipients: this.strings.recipients_label,

                loader_dialog: false,
                mailsended_text: this.strings.mailsended_text,
            }
        },
        methods : {
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
                //const regex = / /i;

                let data = {
                    action : "sendmail",
                    subject : this.strings.subject,
                    recipients : recipients,
                    text : this.message,
                    userid : this.userid,
                    courseid : this.courseid,
                    moduleid : this.moduleid,
                    modulename : this.modulename,
                    pluginsection: this.pluginsection,
                    component: this.component,
                    //target: `${this.target.replace(/ /i, '_').toLowerCase()}_users`,
                    target: this.target,
                    url: window.location.href
                };
                Axios({
                    method:'get',
                    url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                    params : data,
                }).then((response) => {
                    if (response.status == 200 && response.data.ok) {
                        this.$emit('update_dialog', false);
                        this.$refs.form.reset();
                        Alertify.success(this.mailsended_text);
                    } else {
                        Alertify.error(this.strings.api_error_network);
                        this.loader_dialog = false;
                    }
                }).catch((e) => {
                    Alertify.error(this.strings.api_error_network);
                }).finally(() => {
                    this.loader_dialog = false;
                });
            },

            reset () {
                this.$emit('update_dialog', false);
                this.$refs.form.resetValidation();
            },

            closeDialog() {
                this.$emit('update_dialog', false);
            }
        },
    }
    return emailform;
})