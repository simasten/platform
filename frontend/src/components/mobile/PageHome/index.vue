<template>
    <v-toolbar :color="theme">
        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <slot name="toolbar"></slot>

        <v-btn icon @click="gotoAccountDashboard">
            <v-icon>exit_to_app</v-icon>
        </v-btn>
    </v-toolbar>

    <v-sheet
        :color="`${theme}`"
        class="mx-auto position-absolute w-100 rounded-b-xl"
        height="192"
    ></v-sheet>

    <v-responsive
        height="calc(100dvh - (56px + 64px))"
        class="bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4"
        content-class="position-relative"
    >
        <div
            class="position-absolute text-center"
            style="height: 36px; width: calc(100vw - 32px); z-index: 1"
        >
            <div
                class="d-flex flex-column align-center justify-center position-relative"
            >
                <div
                    class="position-relative text-blue-grey mx-auto"
                    style="width: 64px"
                >
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
                    class="text-caption text-white position-absolute font-weight-bold text-uppercase pt-1 text-right"
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
                        {{ page.name }}
                    </div>
                </div>
            </div>
        </div>

        <v-sheet
            class="mt-9 pt-9"
            elevation="1"
            min-height="calc(100% - 52px)"
            rounded="lg"
        >
            <v-card-text :class="dockMenus.length <= 0 ? 'py-2' : ''">
                <v-row dense>
                    <v-col
                        cols="3"
                        v-for="(page, index) in dockMenus"
                        :key="index"
                    >
                        <v-card
                            :border="`border-${theme} thin`"
                            :color="`${theme}-lighten-5`"
                            class="text-center"
                            rounded="md"
                            width="100%"
                            flat
                            @click="openPage(page)"
                        >
                            <v-card-text class="pa-3 pb-1">
                                <v-avatar
                                    :color="theme"
                                    size="36"
                                    style="font-size: 11px"
                                >
                                    <v-icon
                                        :color="highlight"
                                        :icon="page.icon"
                                        size="large"
                                    ></v-icon>
                                </v-avatar>
                            </v-card-text>

                            <v-card-text
                                :class="`text-${theme}-darken-1`"
                                class="pa-1"
                                style="max-height: 38px"
                            >
                                <div
                                    class="text-caption d-flex align-center justify-center overflow-hidden w-100"
                                    style="
                                        font-size: 9px !important;
                                        line-height: 1.1em;
                                        height: 24px;
                                    "
                                >
                                    {{ page.name }}
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-card-text>

            <v-divider>
                <v-chip
                    :color="theme"
                    density="comfortable"
                    size="small"
                    variant="flat"
                >
                    <v-icon>more_horiz</v-icon>
                </v-chip>
            </v-divider>

            <slot :record="record" :pulse="pulse" :theme="theme"></slot>
        </v-sheet>

        <div class="py-2"></div>
    </v-responsive>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "page-home",

    emits: {
        initialized: null,
    },

    props: {
        pageName: {
            type: String,
            default: null,
        },

        pageKey: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        const {
            auth,
            dockMenus,
            highlight,
            module,
            navigationState,
            page,
            pulse,
            railMode,
            record,
            theme,
        } = storeToRefs(store);

        const { getDashboard } = store;

        return {
            getDashboard,

            auth,
            highlight,
            module,
            navigationState,
            page,
            pulse,
            railMode,
            record,
            dockMenus,
            theme,
        };
    },

    created() {
        this.getDashboard((response) => {
            this.$emit("initialized", { record: response });
        });
    },

    methods: {
        gotoAccountDashboard: function () {
            this.$router.push({ name: "account-dashboard" });
        },

        openPage: function (page) {
            this.$router.push({ name: page.slug });
        },
    },
};
</script>
