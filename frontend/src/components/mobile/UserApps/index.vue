<template>
    <v-responsive class="h-100 w-100 overflow-x-hidden overflow-y-auto">
        <v-toolbar :color="`${theme}`">
            <v-toolbar-title class="text-body-2 font-weight-bold"
                >SiMASTEN</v-toolbar-title
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
        </v-toolbar>

        <v-sheet
            :color="`${theme}`"
            class="mx-auto position-absolute w-100 rounded-b-xl"
            height="192"
        ></v-sheet>

        <v-sheet
            :height="
                navigationState ? `calc(100dvh - 120px)` : `calc(100dvh - 64px)`
            "
            class="bg-transparent overflow-x-hidden overflow-y-auto scrollbar-none px-4 pb-4 position-relative"
        >
            <v-sheet
                class="position-absolute"
                color="transparent"
                width="calc(100% - 32px)"
                style="z-index: 1"
            >
                <div class="d-flex justify-center">
                    <v-avatar
                        :color="`${theme}`"
                        class="pa-1 elevation-4"
                        rounded="pill"
                        size="72"
                    >
                        <v-avatar
                            :color="`${theme}-lighten-3`"
                            rounded="pill"
                            size="64"
                        >
                            <v-img
                                :src="
                                    auth.avatar ??
                                    `/avatars/${auth.gender}-avatar.svg`
                                "
                            ></v-img>
                        </v-avatar>
                    </v-avatar>
                </div>
            </v-sheet>

            <v-card class="mt-9 pt-9" elevation="1" rounded="lg" flat>
                <v-card-text class="text-center">
                    <div class="text-body-2 font-weight-medium text-uppercase">
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
                                cols="3"
                                v-for="(module, index) in modules.personal"
                                :key="index"
                            >
                                <v-card
                                    class="text-center"
                                    rounded="md"
                                    width="100%"
                                    @click="openModule(module)"
                                    flat
                                >
                                    <v-card-text class="pa-2 pb-1 text-center">
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
                                            class="text-caption font-weight-medium d-flex align-center justify-center overflow-hidden w-100"
                                            style="
                                                font-size: 10px !important;
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
                                cols="3"
                                v-for="(module, index) in modules.administrator"
                                :key="index"
                            >
                                <v-card
                                    rounded="md"
                                    width="100%"
                                    @click="openModule(module)"
                                    flat
                                >
                                    <v-card-text class="pa-2 pb-1 text-center">
                                        <v-avatar
                                            rounded="pill"
                                            :color="module.color"
                                            style="font-size: 16px"
                                            size="56"
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
                                            class="text-caption font-weight-medium d-flex align-center justify-center overflow-hidden w-100"
                                            style="
                                                font-size: 10px !important;
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
            </v-card>
            <slot></slot>
        </v-sheet>
    </v-responsive>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-apps",

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
            theme,
        } = storeToRefs(store);

        const { getUserModules, mapUserModule, signOut } = store;

        return {
            auth,
            highlight,
            impersonated,
            modules,
            navigationState,
            theme,

            getUserModules,
            mapUserModule,
            signOut,
        };
    },

    created() {
        this.getUserModules();
    },

    methods: {
        openModule: function (module) {
            this.$router.push({ name: module.slug + "-dashboard" });
        },
    },
};
</script>
