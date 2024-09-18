<template>
    <v-app-bar
        :color="`${theme}-lighten-5`"
        height="72"
        scroll-behavior="hide elevate"
        scroll-threshold="87"
    >
        <v-app-bar-nav-icon @click="railMode = !railMode"></v-app-bar-nav-icon>

        <v-toolbar-title class="text-body-2 font-weight-medium"
            >LAYANAN</v-toolbar-title
        >

        <v-spacer></v-spacer>

        <v-btn
            icon
            v-if="impersonated"
            @click="
                $emit('click:impersonateLeave', {
                    mapUserModule: mapUserModule,
                })
            "
        >
            <v-icon>no_accounts</v-icon>
        </v-btn>
    </v-app-bar>

    <v-main style="min-height: 100dvh">
        <v-container>
            <v-sheet class="bg-transparent position-relative px-4 pt-9 pb-4">
                <v-sheet
                    class="position-absolute"
                    color="transparent"
                    width="calc(100% - 32px)"
                    style="top: -7px; z-index: 1"
                >
                    <div class="d-flex justify-center">
                        <form-icon>
                            <v-img
                                :src="
                                    auth.avatar ??
                                    `/avatars/${auth.gender}-avatar.svg`
                                "
                            ></v-img>
                        </form-icon>
                    </div>
                </v-sheet>

                <v-sheet
                    :max-width="maxWidth"
                    class="mx-auto position-relative pt-7"
                    rounded="xl"
                    flat
                >
                    <v-card-text class="text-center">
                        <div
                            class="text-body-2 font-weight-medium text-uppercase"
                        >
                            {{ auth.username }}
                        </div>
                        <div class="text-caption">{{ auth.usermail }}</div>
                    </v-card-text>

                    <template
                        v-if="modules.personal && modules.personal.length > 0"
                    >
                        <v-divider>
                            <v-chip
                                :color="theme"
                                density="comfortable"
                                size="small"
                                variant="flat"
                                >personal</v-chip
                            >
                        </v-divider>

                        <v-card-text>
                            <v-row dense>
                                <v-col
                                    cols="2"
                                    v-for="(module, index) in modules.personal"
                                    :key="index"
                                >
                                    <v-card
                                        class="text-center mx-auto"
                                        rounded="md"
                                        max-width="96"
                                        @click="openModule(module)"
                                        flat
                                    >
                                        <v-card-text
                                            class="pa-4 pb-1 text-center"
                                        >
                                            <v-avatar
                                                :color="module.color"
                                                style="font-size: 16px"
                                                size="56"
                                                rounded="pill"
                                            >
                                                <v-icon
                                                    :color="module.highlight"
                                                    :icon="module.icon"
                                                    size="large"
                                                ></v-icon>
                                            </v-avatar>
                                        </v-card-text>

                                        <v-card-text
                                            :class="`text-${theme}-darken-1 pa-1`"
                                            style="max-height: 38px"
                                        >
                                            <div
                                                class="text-caption font-weight-default d-flex align-center justify-center overflow-hidden w-100"
                                                style="
                                                    line-height: 1.1em;
                                                    height: 24px;
                                                "
                                            >
                                                {{ module.name }}
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </template>

                    <template
                        v-if="
                            modules.administrator &&
                            modules.administrator.length > 0
                        "
                    >
                        <v-divider>
                            <v-chip
                                :color="theme"
                                density="comfortable"
                                size="small"
                                variant="flat"
                                >administrator</v-chip
                            >
                        </v-divider>

                        <v-card-text>
                            <v-row dense>
                                <v-col
                                    cols="2"
                                    v-for="(
                                        module, index
                                    ) in modules.administrator"
                                    :key="index"
                                >
                                    <v-card
                                        class="text-center mx-auto"
                                        rounded="md"
                                        max-width="96"
                                        @click="openModule(module)"
                                        flat
                                    >
                                        <v-card-text
                                            class="pa-4 pb-1 text-center"
                                        >
                                            <v-avatar
                                                :color="module.color"
                                                style="font-size: 16px"
                                                size="56"
                                                rounded="pill"
                                            >
                                                <v-icon
                                                    :color="module.highlight"
                                                    :icon="module.icon"
                                                    size="large"
                                                ></v-icon>
                                            </v-avatar>
                                        </v-card-text>

                                        <v-card-text
                                            :class="`text-${theme}-darken-1 pa-1`"
                                            style="max-height: 38px"
                                        >
                                            <div
                                                class="text-caption font-weight-default d-flex align-center justify-center overflow-hidden w-100"
                                                style="
                                                    line-height: 1.1em;
                                                    height: 24px;
                                                "
                                            >
                                                {{ module.name }}
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </template>
                </v-sheet>
            </v-sheet>
        </v-container>
    </v-main>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-apps",

    props: {
        maxWidth: {
            type: [String, Number],
            default: "900",
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

    emits: {
        "click:impersonateLeave": null,
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        const {
            auth,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
            theme,
        } = storeToRefs(store);

        const { getUserModules, initPage, mapUserModule } = store;

        return {
            auth,
            highlight,
            impersonated,
            modules,
            navigationState,
            railMode,
            theme,

            getUserModules,
            initPage,
            mapUserModule,
        };
    },

    created() {
        this.initPage();
    },

    beforeMount() {
        this.getUserModules();
    },

    methods: {
        openModule: function (module) {
            this.$router.push({ name: module.slug + "-dashboard" });
        },
    },
};
</script>
