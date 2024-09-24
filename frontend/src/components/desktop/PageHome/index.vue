<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-app-bar-nav-icon @click="railMode = !railMode"></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-bold text-uppercase">{{
            module.name
        }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <slot name="toolbar"></slot>

        <v-btn icon @click="gotoAccountDashboard">
            <v-icon>exit_to_app</v-icon>
        </v-btn>
    </v-app-bar>

    <v-main style="min-height: 100dvh">
        <v-container>
            <page-paper max-width="100%">
                <div class="d-flex justify-center mt-3">
                    <v-chip
                        :color="theme"
                        class="text-white"
                        density="comfortable"
                        size="small"
                        variant="flat"
                    >
                        Module Pages
                    </v-chip>
                </div>

                <v-sheet
                    class="mx-auto bg-transparent pa-4"
                    :style="
                        $vuetify.display.width >= 1280
                            ? { maxWidth: '1164px' }
                            : { maxWidth: '640px' }
                    "
                >
                    <v-row justify="center" align="center" dense>
                        <v-col
                            v-for="(page, index) in dockMenus"
                            class="d-flex justify-center"
                            cols="2"
                            lg="1"
                            :key="index"
                        >
                            <v-hover>
                                <template
                                    v-slot:default="{ isHovering, props }"
                                >
                                    <v-sheet
                                        v-bind="props"
                                        :color="`${theme}-lighten-5`"
                                        class="mt-4 cursor-pointer border-thin"
                                        width="84"
                                        rounded="lg"
                                        @click="openPage(page)"
                                    >
                                        <v-sheet
                                            class="position-relative text-center"
                                            style="margin-top: 40px"
                                        >
                                            <div
                                                class="d-flex justify-center position-absolute w-100"
                                            >
                                                <v-avatar
                                                    :color="`${highlight}-darken-1`"
                                                    :class="
                                                        isHovering
                                                            ? 'elevation-6'
                                                            : 'elevation-2'
                                                    "
                                                    class="position-absolute"
                                                    rounded="lg"
                                                    size="56"
                                                    style="bottom: 0"
                                                >
                                                    <v-icon
                                                        :color="
                                                            isHovering
                                                                ? `${theme}-darken-2`
                                                                : `${theme}-lighten-1`
                                                        "
                                                        :icon="page.icon"
                                                        size="26"
                                                    ></v-icon>
                                                </v-avatar>
                                            </div>
                                        </v-sheet>

                                        <v-card-text
                                            class="px-2 py-2 d-flex align-center justify-center text-center text-caption"
                                            style="
                                                line-height: 1.1;
                                                height: 42px;
                                            "
                                        >
                                            {{ page.name }}
                                        </v-card-text>
                                    </v-sheet>
                                </template>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-sheet>
            </page-paper>

            <slot :record="record" :store="store" :theme="theme"></slot>
        </v-container>
    </v-main>
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
        maxWidth: {
            type: String,
            default: "900px",
        },

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
            page,
            railMode,
            record,
            theme,
        } = storeToRefs(store);

        const { getDashboard } = store;

        return {
            auth,
            dockMenus,
            highlight,
            module,
            page,
            railMode,
            record,
            theme,

            getDashboard,
            store,
        };
    },

    mounted() {
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
