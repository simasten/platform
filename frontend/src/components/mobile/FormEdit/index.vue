<template>
    <v-toolbar :color="theme">
        <v-btn icon @click="openFormData">
            <v-icon class="with-shadow">arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn v-if="!hideUpdate" icon @click="postFormEdit">
            <v-icon class="with-shadow">save</v-icon>

            <v-tooltip activator="parent" location="bottom">Simpan</v-tooltip>
        </v-btn>

        <v-btn
            v-if="withHelpdesk"
            :color="helpState ? 'white' : `${theme}-lighten-3`"
            icon
            @click="helpState = !helpState"
        >
            <v-icon
                :style="
                    helpState
                        ? 'transform: rotate(180deg)'
                        : 'transform: rotate(0deg)'
                "
                class="with-shadow"
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
                        edit: {{ record[key] }}
                    </div>
                </div>
            </div>
        </v-sheet>

        <v-sheet
            class="mt-9 pt-9"
            elevation="1"
            min-height="200px"
            rounded="lg"
        >
            <slot
                :combos="combos"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </v-sheet>

        <div class="py-2"></div>
    </v-responsive>

    <form-help mode="edit" :withActivityLogs="withActivityLogs">
        <template v-slot:forminfo>
            <slot
                name="forminfo"
                :record="record"
                :theme="theme"
                :store="store"
            ></slot>
        </template>

        <template v-slot:helpdesk>
            <slot
                name="helpdesk"
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
    name: "form-edit",

    props: {
        beforePost: Function,
        hideUpdate: Boolean,
        dataFromStore: Boolean,
        routePrefix: String,
        withHelpdesk: Boolean,
        withActivityLogs: Boolean,
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
            page,
            pageKey,
            record,
            theme,
        } = storeToRefs(store);

        const { getPageData, openFormData, postFormEdit } = store;

        return {
            combos,
            helpState,
            highlight,
            key,
            page,
            pageKey,
            record,
            theme,

            getPageData,
            openFormData,
            postFormEdit,

            store,
        };
    },

    mounted() {
        this.getPageData(this.dataFromStore);
    },
};
</script>
