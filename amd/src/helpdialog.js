define([],
    function (){
        const helpdialog = {
            template:`
                <v-main mt-10>
                    <v-row>
                        <v-dialog
                            v-model="dialog"
                            max-width="700px"
                            @click:outside="closeDialog()"
                            @keydown.esc="closeDialog()"
                        >    
                            <v-card>
                                <v-card-title class="headline lighten-2 d-flex justify-center help-dialog-title">
                                    <span v-text="title" class="help-modal-title mr-2"></span><v-icon color="white">help_outline</v-icon>
                                </v-card-title>
                                <v-card-text class="pt-4 pb-4 pr-8 pl-8 help-dialog-content">
                                    <template v-for="(help, index, key) in contents">
                                        <v-layout class="mb-2" :key="key" column>
                                            <v-flex class="d-flex justify-center">
                                                <span class="fliplearning-sub-title mb-2" v-html="help.title"></span>
                                            </v-flex>
                                            <p v-html="help.description" class="text-justify"></p>
                                        </v-layout>
                                    </template>
                                </v-card-text>
                                <v-divider class="ma-0"></v-divider>
                                <v-card-actions class="d-flex justify-center help-dialog-footer">
                                    <v-btn text @click="closeDialog" v-text="exit" class="ma-0 fml-btn-secondary"></v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-row>
               </v-main>
                `,
            props:['dialog', 'title', 'contents', 'exit'],
            methods : {
                closeDialog() {
                    this.$emit('update_dialog', false);
                },
            },
        }
        return helpdialog;
    })