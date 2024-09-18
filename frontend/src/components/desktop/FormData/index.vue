<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        :order="1"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-btn
            icon
            v-if="parentName || navbackTo"
            @click="
                navbackTo
                    ? $router.push({ name: navbackTo })
                    : $router.push({ name: parentName })
            "
        >
            <v-icon>arrow_back</v-icon>
        </v-btn>

        <v-app-bar-nav-icon
            v-else
            @click="railMode = !railMode"
        ></v-app-bar-nav-icon>

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
            <v-icon>add</v-icon>
        </v-btn>

        <v-btn
            :disabled="hasSelected"
            icon
            @click="sidenavState = !sidenavState"
        >
            <v-icon>{{ sidenavState ? "close" : "filter_list" }}</v-icon>
        </v-btn>
    </v-app-bar>

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

    <v-main style="min-height: 100dvh">
        <v-container>
            <v-sheet
                :max-width="maxWidth"
                class="bg-transparent mx-auto position-relative px-0 pt-9 pb-4"
            >
                <v-sheet
                    class="position-absolute"
                    color="transparent"
                    width="calc(100% - 32px)"
                    style="top: -7px; z-index: 1"
                >
                    <div class="d-flex justify-center">
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
                    class="mx-auto position-relative pt-7"
                    rounded="xl"
                    flat
                >
                    <v-list
                        class="bg-transparent overflow-y-auto overflow-x-hidden scrollbar-none"
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

                        <template
                            v-if="
                                meta.current_page &&
                                meta.current_page < meta.last_page
                            "
                        >
                            <v-list-item
                                v-intersect="onIntersect"
                            ></v-list-item>
                        </template>
                    </v-list>
                </v-sheet>
            </v-sheet>
        </v-container>
    </v-main>
    <!-- <v-sheet color="transparent" class="position-relative">
        <v-toolbar :color="theme">
            <v-app-bar-nav-icon
                @click="railMode = !railMode"
            ></v-app-bar-nav-icon>

            <v-toolbar-title
                class="text-body-2 font-weight-bold text-uppercase"
                >{{ module.name }}</v-toolbar-title
            >

            <v-spacer></v-spacer>

            <v-btn
                v-if="!disableCreate && !hasSelected"
                :color="highlight"
                icon="add"
                @click="openFormCreate"
            ></v-btn>

            <v-btn
                icon="folder_open"
                v-if="hasSelected"
                @click="openFormShow"
            ></v-btn>

            <v-btn
                :color="highlight"
                icon="delete"
                v-if="hasSelected && showDelete"
            ></v-btn>

            <v-btn
                :disabled="hasSelected"
                :color="sidenavState ? 'white' : `${theme}-lighten-3`"
                icon="filter_list"
                @click="sidenavState = !sidenavState"
            ></v-btn>
        </v-toolbar>

        <v-sheet
            :color="`${theme}-lighten-4`"
            class="mx-auto position-absolute w-100"
            height="100%"
        ></v-sheet>

        <v-sheet
            :color="`${theme}`"
            class="mx-auto position-absolute w-100 rounded-b-xl"
            height="192"
        ></v-sheet>

        <v-sheet
            class="position-relative bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
            height="calc(100dvh - 72px)"
            width="100%"
        >
            <div
                class="position-absolute text-center"
                style="width: calc(100% - 32px); z-index: 1"
            >
                <div
                    :style="`max-width: ${maxWidth}`"
                    class="d-flex flex-column align-center justify-center position-relative mx-auto"
                >
                    <v-card :color="`${theme}`" rounded="pill">
                        <v-card-text class="pa-1">
                            <v-avatar
                                :color="`${highlight}-lighten-2`"
                                size="64"
                                style="font-size: 22px"
                            >
                                <v-icon :color="`${theme}-darken-1`">{{
                                    page.icon
                                }}</v-icon>
                            </v-avatar>
                        </v-card-text>
                    </v-card>

                    <div
                        :class="`text-${theme}-lighten-4`"
                        class="text-caption text-white position-absolute pt-3 font-weight-bold text-uppercase"
                        style="top: 0; right: 0"
                    >
                        tabel {{ page.name }}
                    </div>
                </div>
            </div>

            <v-card
                :style="`max-width: ${maxWidth}`"
                class="mt-9 pt-9 mx-auto"
                rounded="lg"
                flat
            >
                <v-data-table-server
                    v-model="selected"
                    :headers="headers"
                    :items="records"
                    :items-length="totalRecords"
                    :items-per-page="itemsPerPage"
                    :loading="loading"
                    density="comfortable"
                    item-value="name"
                    select-strategy="single"
                    @click:row="clickOnRow"
                    @update:options="loadItems"
                    return-object
                    show-select
                ></v-data-table-server>
            </v-card>
        </v-sheet>
    </v-sheet> -->
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

        maxWidth: {
            type: [String, Number],
            default: "900",
        },

        navbackTo: String,

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

    setup() {
        const store = usePageStore();

        store.helpState = false;
        store.sidenavState = true;

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

    beforeUnmount() {
        if (this.parentName || this.navbackTo) {
            this.navigationState = true;
        }
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
