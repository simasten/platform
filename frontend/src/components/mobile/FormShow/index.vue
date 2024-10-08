<template>
    <v-toolbar :color="theme">
        <v-btn
            icon
            @click="
                manualBacknav ? $emit('click:backnav', $event) : openFormData()
            "
        >
            <v-icon class="with-shadow">arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <template v-if="softdelete">
            <v-btn icon>
                <v-icon class="with-shadow">restore</v-icon>

                <form-confirm icon="restore" title="pulihkan">
                    <v-card-text>
                        Proses ini akan memulihkan data ini dari status trashed.
                    </v-card-text>

                    <template v-slot:actions="{ isActive }">
                        <v-spacer></v-spacer>

                        <v-btn
                            :color="`${theme}-lighten-2`"
                            class="text-white mr-2"
                            variant="flat"
                            @click="isActive.value = false"
                            >batal</v-btn
                        >

                        <v-btn
                            color="deep-orange"
                            text="Pulihkan"
                            @click="
                                postFormRestore(() => (isActive.value = false))
                            "
                        ></v-btn>
                    </template>
                </form-confirm>
            </v-btn>

            <v-btn color="orange" icon>
                <v-icon class="with-shadow">delete_forever</v-icon>

                <form-confirm icon="delete_forever" title="Hapus Permanen?">
                    <v-card-text>
                        Proses ini akan menghapus data secara permanen, proses
                        ini tidak dapat di pulihkan setelah di lakukan.
                    </v-card-text>

                    <template v-slot:actions="{ isActive }">
                        <v-spacer></v-spacer>

                        <v-btn
                            :color="`${theme}-lighten-2`"
                            class="text-white mr-2"
                            variant="flat"
                            @click="isActive.value = false"
                            >batal</v-btn
                        >

                        <v-btn
                            color="deep-orange"
                            text="Hapus"
                            @click="
                                postFormForceDelete(
                                    () => (isActive.value = false)
                                )
                            "
                        ></v-btn>
                    </template>
                </form-confirm>
            </v-btn>
        </template>

        <template v-else>
            <v-btn v-if="!hideEdit" icon @click="openFormEdit">
                <v-icon class="with-shadow">edit</v-icon>

                <v-tooltip activator="parent" location="bottom">Ubah</v-tooltip>
            </v-btn>

            <v-btn v-if="!hideDelete" color="orange" icon>
                <v-icon class="with-shadow">delete</v-icon>

                <form-confirm icon="delete" title="Hapus data ini?">
                    <div class="text-caption text-grey-darken-1">
                        Proses ini akan juga menghapus semua data yang terkait
                        pada data ini.
                    </div>

                    <template v-slot:actions="{ isActive }">
                        <v-row dense>
                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="outlined"
                                    block
                                    @click="isActive.value = false"
                                    >BATAL</v-btn
                                >
                            </v-col>

                            <v-col cols="6">
                                <v-btn
                                    :color="theme"
                                    rounded="pill"
                                    variant="flat"
                                    block
                                    @click="
                                        postFormDelete(
                                            () => (isActive.value = false)
                                        )
                                    "
                                    >HAPUS</v-btn
                                >
                            </v-col>
                        </v-row>
                    </template>
                </form-confirm>

                <v-tooltip activator="parent" location="bottom"
                    >Hapus</v-tooltip
                >
            </v-btn>
        </template>

        <v-btn
            v-if="withHelpdesk"
            :color="helpState ? 'white' : `${theme}-lighten-3`"
            icon
            @click="helpState = !helpState"
        >
            <v-icon
                class="with-shadow"
                :style="
                    helpState
                        ? 'transform: rotate(180deg)'
                        : 'transform: rotate(0deg)'
                "
                >menu_open</v-icon
            >

            <v-tooltip activator="parent" location="bottom"
                >Informasi</v-tooltip
            >
        </v-btn>
    </v-toolbar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-absolute w-100 rounded-b-xl"
        height="192"
    ></v-sheet>

    <v-responsive
        height="calc(100dvh - 64px)"
        class="bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
        content-class="position-relative"
    >
        <v-sheet
            class="position-absolute text-center w-100"
            color="transparent"
            style="z-index: 1"
        >
            <div class="d-flex justify-center position-relative">
                <form-icon></form-icon>

                <div
                    :class="`text-${theme}-lighten-4`"
                    class="text-caption text-white position-absolute font-weight-bold text-uppercase text-left"
                    style="
                        top: 8px;
                        left: 0;
                        font-size: 0.63rem !important;
                        width: calc(50% - 30px);
                    "
                >
                    <div
                        class="d-inline-block text-truncate"
                        style="max-width: 100%"
                    >
                        {{ title }}
                    </div>
                </div>

                <div
                    v-if="!hideDataTag"
                    :class="`text-${theme}-lighten-4`"
                    class="text-caption text-white position-absolute font-weight-bold text-uppercase text-right"
                    style="
                        font-size: 0.63rem !important;
                        top: 8px;
                        right: 0;
                        width: calc(50% - 30px);
                    "
                >
                    <div
                        class="d-inline-block text-truncate"
                        style="max-width: 100%"
                    >
                        show: {{ record[key] }}
                    </div>
                </div>
            </div>
        </v-sheet>

        <v-sheet
            class="position-relative mt-9 pt-9"
            min-height="200px"
            elevation="1"
            rounded="lg"
        >
            <slot
                :combos="combos"
                :mapResponseData="mapResponseData"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </v-sheet>

        <div class="py-2"></div>
    </v-responsive>

    <form-help mode="show" :withActivityLogs="withActivityLogs">
        <template v-slot:forminfo>
            <slot name="forminfo" :theme="theme"></slot>
        </template>

        <template v-slot:helpdesk>
            <slot
                name="helpdesk"
                :mapResponseData="mapResponseData"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </template>
    </form-help>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-show",

    props: {
        beforePost: Function,
        blankForm: Boolean,
        contentClass: String,
        dataFromStore: Boolean,
        hideEdit: Boolean,
        hideDataTag: Boolean,
        hideDelete: Boolean,
        manualBacknav: Boolean,
        routePrefix: String,
        width: {
            type: String,
            default: "500px",
        },
        withHelpdesk: Boolean,
        withActivityLogs: Boolean,
    },

    emits: {
        "click:backnav": null,
    },

    setup(props) {
        const store = usePageStore();

        store.beforePost = props.beforePost;
        store.activityLog = props.withActivityLogs;
        store.routePrefix = props.routePrefix;

        const {
            combos,
            helpState,
            highlight,
            key,
            navigationState,
            page,
            pageKey,
            softdelete,
            record,
            theme,
            title,
        } = storeToRefs(store);

        const {
            getPageData,
            mapResponseData,
            openFormData,
            openFormEdit,
            postFormDelete,
            postFormForceDelete,
            postFormRestore,
        } = store;

        return {
            combos,
            helpState,
            highlight,
            key,
            navigationState,
            page,
            pageKey,
            record,
            softdelete,
            theme,
            title,

            getPageData,
            mapResponseData,
            openFormData,
            openFormEdit,
            postFormDelete,
            postFormForceDelete,
            postFormRestore,

            store,
        };
    },

    mounted() {
        this.getPageData(this.dataFromStore);
    },
};
</script>
