<template>
    <v-layout :class="`bg-${theme}-lighten-5`">
        <v-navigation-drawer
            color="white"
            rail-width="68"
            disable-resize-watcher
            permanent
            :rail="railMode"
        >
            <template v-slot:prepend>
                <div class="d-flex align-center px-1" style="height: 72px">
                    <img src="/assets/pwa-60x60.png" style="margin-left: 0px" />

                    <div class="d-flex flex-column pl-3">
                        <div class="text-h5 font-brand">
                            SiMAS<span class="sub-color">TEN</span>
                        </div>

                        <div
                            class="text-caption text-grey"
                            style="line-height: 1"
                        >
                            version 3.0.0
                        </div>
                    </div>
                </div>
                <v-divider></v-divider>
                <!-- <v-toolbar :color="theme" rounded="t-lg">
                    <v-avatar class="ml-2 mr-1" :color="`${theme}-lighten-3`">
                        <v-img
                            :src="
                                auth.avatar ??
                                `/avatars/${auth.gender}-avatar.svg`
                            "
                        ></v-img>
                    </v-avatar>

                    <v-toolbar-title class="text-white" v-if="auth">
                        <div class="text-body-2">
                            {{ auth.username }}
                        </div>
                        <div class="text-caption" style="line-height: 1">
                            <small>{{ auth.usermail }}</small>
                        </div>
                    </v-toolbar-title>
                </v-toolbar> -->
            </template>

            <template v-slot:default>
                <div
                    class="position-relative bg-white h-100 py-2 px-1 overflow-hidden"
                >
                    <v-list
                        :active-class="`bg-${theme}-darken-1`"
                        class="h-100"
                        nav
                    >
                        <v-list-item
                            v-for="(page, index) in appsMenus"
                            :key="index"
                            :to="{ name: page.slug }"
                        >
                            <template v-slot:prepend="{ isActive }">
                                <v-icon
                                    :color="
                                        isActive
                                            ? `white`
                                            : `${theme}-lighten-2`
                                    "
                                    :icon="page.icon"
                                ></v-icon>
                            </template>

                            <template v-slot:default="{ isActive }">
                                <v-list-item-title
                                    :class="
                                        isActive
                                            ? `white`
                                            : `text-${theme}-lighten-2`
                                    "
                                    >{{ page.name }}</v-list-item-title
                                >
                            </template>
                        </v-list-item>
                    </v-list>
                </div>
            </template>
        </v-navigation-drawer>

        <router-view key="userbase" />

        <v-overlay
            :model-value="overlay"
            class="align-center justify-center"
            scroll-strategy="block"
            persistent
        >
            <v-progress-circular
                :color="theme"
                :width="3"
                size="64"
                indeterminate
            >
                <template v-slot:default><small>loading</small></template>
            </v-progress-circular>
        </v-overlay>

        <v-snackbar
            :color="snackbar.color"
            :timeout="1500"
            v-model="snackbar.state"
            multi-line
        >
            {{ snackbar.text }}

            <template v-slot:actions>
                <v-btn
                    color="white"
                    variant="text"
                    @click="snackbar.state = false"
                >
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </v-layout>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-base",

    props: {
        moduleName: {
            type: String,
            default: null,
        },
    },

    setup(props) {
        const store = usePageStore();

        store.moduleName = props.moduleName;

        const {
            auth,
            appsMenus,
            geoInitialized,
            navigationState,
            overlay,
            railMode,
            snackbar,
            theme,
        } = storeToRefs(store);
        const { initModule } = store;

        return {
            auth,
            appsMenus,
            geoInitialized,
            navigationState,
            overlay,
            railMode,
            snackbar,
            theme,

            initModule,
        };
    },

    created() {
        if (!this.auth) {
            this.$router.push({ name: "welcome-page" });
            return;
        }

        this.initModule({ mobile: false });
    },
};
</script>
