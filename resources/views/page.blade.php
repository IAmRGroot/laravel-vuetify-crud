@php
    /** @var IAmRGroot\VuetifyCRUD\Controllers\VuetifyCRUDController $crud */
@endphp
<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="items"
            :search="search"
            :loading="loading"
            :options="{
                itemsPerPage: 15,
                sortDesc: [false],
            }"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-toolbar flat>
                    <v-row
                        align="start"
                        justify="space-between"
                        no-gutters
                        class="pt-5"
                    >
                        <v-col
                            cols="10"
                            md="2"
                        >
                            <v-text-field
                                v-model="search"
                                :label="$t('msg.search')"
                                :append-outer-icon="server_side ? 'mdi-arrow-right' : undefined"
                                prepend-icon="mdi-magnify"
                                single-line
                                hide-details
                                clearable
                                @click:append-outer="doSearch()"
                            />
                        </v-col>

                        <v-col
                            cols="2"
                            md="2"
                            class="text-right"
                        >
                            <v-btn
                                color="primary"
                                dark
                                @click="newItem"
                            >
                                <v-icon
                                    dark
                                    left
                                >
                                    mdi-plus
                                </v-icon>
                                <span>
                                    @{{ $t('msg.buttons.new', { attribute: $t('{{{ $crud->getTranslation() }}}')}) }}
                                </span>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-toolbar>
            </template>
        </v-data-table>

        <v-dialog
            v-model="dialog"
            :max-width="1000"
            persistent
        >
            <v-card :loading="loading">
                <v-card-title>
                    <v-row
                        align="start"
                        justify="space-between"
                        no-gutters
                    >
                        <span
                            class="text-h5"
                            style="max-width: calc(100% - 35px)"
                        >
                            @{{ form_title }}
                        </span>
                        <v-btn
                            icon
                            small
                            @click="dialog = false"
                        >
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-row>
                </v-card-title>

                <v-card-text>
                    TODO{!! $crud->renderForm() !!}
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
const crudRoute = (id = '') => `async/{{ $crud->getName() }}/${id}`;

export default {
    data: () => ({
        items: [],
        selected: {},
        loading: true,
        dialog: false,
        search: '',
        default: {!! $crud->getDefault() !!},
    }),
    computed: {
        form_title() {
            return selected === -1 ?
                this.$t('msg.buttons.new', { attribute: this.$t('{{ $crud->getTranslation() }}')}) :
                this.$t('msg.buttons.edit', { attribute: this.$t('{{ $crud->getTranslation() }}')});
        },
        headers() {
            return {{ $crud->getHeaders() }};
        }
    },
    created() {
        this.fetch();
    },
    methods: {
        fetch() {
            this.loading = true;
            axios.get(
                crudRoute()
            ).then(response => {
                this.items = response.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        newItem() {
            this.selected = Object.assign({}, this.selected, this.default);
            this.dialog = true;
        },
        edit(item) {
            this.selected = Object.assign({}, this.selected, item);
            this.dialog = true;
        },
        updateItem(updated) {
            let found = this.items.find(item => {
                return item.id === updated.id;
            });

            if (found) {
                Object.assign(found, updated);
            }
        },
        dialogEditSavedPrinter(saved_item) {
            if (this.selected.id > -1) {
                Object.assign(this.items.find(item => {
                    return item.id === this.selected.id;
                }), saved_item);
            } else {
                this.items.push(printer);
            }
            this.dialog = false;
        },
        async save() {
            if (this.$refs.form && ! await this.$refs.form.validate()) {
                return;
            }

            let route_to_use = getCrud();
            let method = 'PUT';
            if (this.item.id > 0) {
                route_to_use = getCrud(`/${selected.id}`);
                method = 'PATCH';
            }

            axios.request({
                method: method,
                url: route_to_use,
                data: {
                    ...selected,
                }
            }).then(({ data }) => {
                this.updateItem(data);
            }).catch( error => {
                if (this.$refs.form) {
                    this.$refs.form.setErrors(
                        error.response.data.errors
                    );
                }
            });
        },
        remove(item_to_delete) {
            axios.delete(
                getCrud(`/${item_to_delete.id}`),
            ).then(() => {
                this.items = this.items.filter(item => {
                    return item.id !== item_to_delete.id;
                });
            });
        },
    },
};
</script>


