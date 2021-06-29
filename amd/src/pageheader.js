define(['local_fliplearning/axios'], function (Axios){
    const pageheader = {
        template:`
            <v-layout 
                class="font-weight-bold fliplearning-page-title justify-space-between align-center" 
                id="page-header"
            >
                <v-flex class="d-flex pa-4">
                    <span v-text="pagetitle"></span>
                </v-flex>
            
                <v-flex id="fml-group-selector">
                    <v-select
                            attach 
                            v-model="selectedgroup" 
                            v-if="usegroupselector()" 
                            prepend-icon="group" 
                            @change="update_group()"
                            :items="groups" 
                            item-text="name" 
                            item-value="id">
                    </v-select>
                </v-flex>
            
                <v-flex 
                    class="d-flex justify-end align-center flex-grow-0 fliplearning-help-button pa-4 ml-8" 
                    @click="openHelpModal()"
                >
                    <span class="mr-2 caption" v-text="helptitle"></span>
                    <v-icon :color="'#ffffff'">help_outline</v-icon>
                </v-flex>
            
                <v-dialog v-model="dialog" width="500" class="help-dialog">
                    <v-card>
                        <v-card-title class="headline lighten-2 d-flex justify-center help-dialog-title">
                            <span v-text="helptitle" class="help-modal-title mr-2"></span><v-icon color="white">help_outline</v-icon>
                        </v-card-title>
                        <v-card-text class="pt-4 pb-4 pr-8 pl-8 help-dialog-content">
                            <template v-for="(help, index, key) in helpcontents">
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
                            <v-btn text @click="dialog = false" v-text="exitbutton" class="ma-0 fml-btn-secondary"></v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-layout>`,
        props:[
            'pagetitle',
            'helptitle',
            'helpcontents',
            'exitbutton',
            'groups',
            'courseid',
            'userid',
            'section',
        ],
        data(){
            return{
                dialog : false,
                selectedgroup : null,
            }
        },
        mounted(){
            this.set_selected_group();
        },
        methods : {
            openHelpModal () {
                //console.log('Help dialog');
                this.dialog = true;
                this.saveInteraction();
            },

            update_group(){
                let data = {
                    action : "changegroup",
                    courseid : this.courseid,
                    userid : this.userid,
                    groupid : this.selectedgroup,
                    url: window.location.href,
                }
                Axios({
                    method:'get',
                    url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                    params : data,
                }).then((response) => {
                    location.reload();
                }).catch((e) => {
                    if(confirm("Error al cambiar de grupo. Necesitamos actualizar para evitar errores.")){
                        location.reload()
                    }else{
                        location.reload()
                    }
                });
            },

            usegroupselector(){
                return this.groups && this.groups.length > 0;
            },

            set_selected_group(){
                if(!this.usegroupselector()) {
                    return null;
                }
                this.groups.forEach(group => {
                    if(group.selected){
                        this.selectedgroup = group;
                    }
                })
                if(!this.selectedgroup && typeof(this.groups[0]) != 'undefined'){
                    this.groups[0].selected = true;
                    this.selectedgroup = this.groups[0];
                }
            },

            saveInteraction () {
                let data = {
                    action : "saveinteraction",
                    pluginsection : this.section,
                    component : this.section,
                    interaction : 'viewed',
                    target : 'section_help_dialog',
                    url: window.location.href,
                    interactiontype: 3,
                    courseid : this.courseid,
                    userid : this.userid,
                };
                Axios({
                    method:'get',
                    url: `${M.cfg.wwwroot}/local/fliplearning/ajax.php`,
                    params : data,
                }).then((r) => {}).catch((e) => {});
            },
        }
    }
    return pageheader;
})