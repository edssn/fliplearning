define([], function (){
    const emailform = {
        template:`
                <v-main mt-10>
                    <v-dialog v-model="dialog" width="1000" persistent>
                        <v-card>
                            <v-card-title class="headline grey lighten-2">
                                <span v-text="emailform_title"></span>
                            </v-card-title>


                            <v-chip class="ma-2" label>
                                <span v-text="cc"></span>
                            </v-chip>
                            <template v-for="(user, index, key) in selected_users">
                                <v-chip class="ma-2" close >
                                    <img :src="get_picture_url(user.id)" width="25px" heigth="25px" class="rounded-circle"/>
                                    <span>{{user.firstname}} {{user.lastname}}</span>
                                </v-chip>
                            </template>

                            <v-form ref="form" v-model="valid_form">
                                <v-text-field
                                        v-model="subject"
                                        label="Agrega un Asunto"
                                        :rules="subject_rules"
                                        required
                                ></v-text-field>

                                <v-textarea
                                        v-model="message"
                                        label="Escribe un mensaje"
                                        :rules="message_rules"
                                        required
                                ></v-textarea>

                                <v-btn @click="submit" color="primary" :disabled="!valid_form">
                                    <span v-text="submit_button"></span>
                                </v-btn>
                                <v-btn @click="reset" color="error">
                                    <span v-text="cancel_button"></span>
                                </v-btn>

                            </v-form>

                        </v-card>
                    </v-dialog>
                    <v-divider></v-divider>
               </v-main>
                `,
        props:['dialog', 'selected_users'],
        data(){
            return{
                valid_form: true,
                subject: 'Envío de Tarea 1',
                subject_rules: [
                    v => !!v || 'Asunto es requerido',
                ],
                message: '',
                message_rules: [
                    v => !!v || 'Mensage es requerido',
                ],
                submit_button: 'Enviar',
                cancel_button: 'Cancelar',
                emailform_title: 'Enviar Correo',
                cc: 'Para',
            }
        },
        mounted(){
            console.log(this.dialog);
            console.log(this.selected_users);
            console.log(this.subject);
        },
        methods : {
            get_picture_url(userid){
                let url = `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                return url;
            },

            submit () {
                console.log('submit');
                // this.dialog = false;
                // let new_dialog = false;
                this.$emit('update_dialog', false);
                this.$refs.form.reset();
            },

            reset () {
                console.log('reset');
                // this.dialog = false;
                // let new_dialog = false;
                this.$emit('update_dialog', false);
                this.$refs.form.reset();
            },
        },
    }
    return emailform;
})