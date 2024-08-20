<template>
    <v-toolbar :color="theme">
        <v-btn
            icon
            v-if="parentName"
            @click="
                manualBacknav
                    ? $emit('click:backnav', $event)
                    : $router.push({ name: parentName })
            "
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            page.name ?? module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn
            v-if="!disableCreate && !hasSelected"
            :color="highlight"
            icon
            @click="openFormCreate"
        >
            <v-icon class="with-shadow">add</v-icon>
        </v-btn>

        <v-btn
            :disabled="hasSelected"
            :color="sidenavState ? 'white' : `${theme}-lighten-3`"
            icon
            @click="sidenavState = !sidenavState"
        >
            <v-icon class="with-shadow">filter_list</v-icon>
        </v-btn>
    </v-toolbar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-absolute w-100 rounded-b-xl"
        height="192"
    ></v-sheet>

    <v-responsive
        :height="
            navigationState ? `calc(100dvh - 120px)` : `calc(100dvh - 64px)`
        "
        class="bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
        content-class="position-relative"
    >
        <v-sheet
            class="position-absolute text-center w-100"
            color="transparent"
            style="z-index: 1"
        >
            <div class="d-flex justify-center position-relative">
                <div class="text-blue-grey mx-auto" style="width: 64px">
                    <div class="circle">
                        <div class="position-relative h-100 w-100 text-white">
                            <v-avatar
                                :color="`${theme}-lighten-4`"
                                elevation="6"
                                size="52"
                            >
                                <v-icon color="grey-darken-2">{{
                                    page.icon
                                }}</v-icon>
                            </v-avatar>
                        </div>
                    </div>
                </div>

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
                        SEMUA DATA
                    </div>
                </div>
            </div>
        </v-sheet>

        <v-sheet
            class="mt-9 pt-7"
            elevation="1"
            rounded="lg"
            min-height="calc(100% - 52px)"
        >
            <v-list
                class="bg-transparent"
                :active-class="
                    showDelete
                        ? `bg-white elevation-6 text-grey with-delete`
                        : `bg-white elevation-6 text-grey`
                "
                lines="two"
                selectable
                @update:selected="setSelected"
            >
                <template v-for="(record, index) in records">
                    <slot
                        name="mobile"
                        :index="index"
                        :record="record"
                        :showDelete="showDelete"
                        :theme="theme"
                    >
                        <item-data
                            :chip="chip"
                            :subtitle="subtitle"
                            :show-delete="showDelete"
                            :value="record"
                        ></item-data>
                    </slot>
                </template>

                <template v-if="records.length <= 0">
                    <slot>
                        <div
                            class="d-flex align-center justify-center text-body-2 text-center text-grey"
                            style="height: calc(100dvh - 216px)"
                        >
                            Data tidak ditemukan
                        </div>
                    </slot>
                </template>

                <template v-else>
                    <div
                        v-if="
                            meta.current_page &&
                            meta.current_page < meta.last_page
                        "
                        class="d-flex align-center justify-center py-2"
                        style="width: 100%"
                        v-intersect="onIntersect"
                    ></div>
                </template>
            </v-list>
        </v-sheet>

        <div class="py-2"></div>
    </v-responsive>

    <page-filter :withSync="withSync">
        <template v-slot:syncdata="{ mapResponseData, parent }">
            <slot
                name="syncdata"
                :mapResponseData="mapResponseData"
                :params="params"
                :parent="parent"
                :store="store"
                :theme="theme"
            ></slot>
        </template>

        <template v-slot:forminfo>
            <slot name="forminfo" :store="store" :theme="theme"></slot>
        </template>

        <template v-slot:helpdesk>
            <slot name="helpdesk" :store="store" :theme="theme"></slot>
        </template>

        <template v-slot:utility>
            <slot name="utility" :store="store" :theme="theme"></slot>
        </template>
    </page-filter>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-data",

    props: {
        chip: {
            type: String,
            default: "chip",
        },

        disableCreate: Boolean,
        manualBacknav: Boolean,

        subtitle: {
            type: String,
            default: "subtitle",
        },

        showDelete: {
            type: Boolean,
            default: false,
        },

        withSync: Boolean,
    },

    emits: {
        "click:backnav": null,
    },

    setup() {
        const store = usePageStore();

        store.helpState = false;

        const {
            formStateLast,
            hasSelected,
            headers,
            highlight,
            itemsPerPage,
            loading,
            meta,
            module,
            navigationState,
            page,
            pageKey,
            params,
            parentName,
            records,
            railMode,
            sidenavState,
            selected,
            title,
            theme,
            totalRecords,
        } = storeToRefs(store);

        const { getPageDatas, openFormCreate, openFormShow, setSelected } =
            store;

        return {
            formStateLast,
            hasSelected,
            headers,
            highlight,
            itemsPerPage,
            loading,
            meta,
            module,
            navigationState,
            page,
            pageKey,
            params,
            parentName,
            records,
            railMode,
            sidenavState,
            selected,
            title,
            theme,
            totalRecords,

            getPageDatas,
            openFormCreate,
            openFormShow,
            setSelected,

            store,
        };
    },

    methods: {
        onIntersect: function (isIntersecting) {
            if (isIntersecting) {
                this.loading = true;
                this.params.page = this.params.page + 1;
            }
        },
    },

    watch: {
        params: {
            handler: function (newOptions) {
                this.getPageDatas(newOptions);
            },

            deep: true,
            immediate: true,
        },
    },
};
</script>
