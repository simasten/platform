<template>
    <v-responsive class="h-100 w-100 overflow-hidden">
        <v-toolbar :color="`${theme}`">
            <v-toolbar-title class="text-body-2 font-weight-bold"
                >SiMASTEN</v-toolbar-title
            >

            <v-spacer></v-spacer>

            <v-btn icon @click="$emit('click:notification')">
                <v-icon>notifications</v-icon>
            </v-btn>

            <v-btn icon>
                <v-icon class="with-shadow">power_settings_new</v-icon>

                <form-confirm icon="door_back" title="Keluar dari SiMASTEN?">
                    <div class="text-caption text-grey-darken-1">
                        Saat Anda keluar dari aplikasi ini, semua data temporer
                        yang tersimpan pada perangkat ini juga akan di hapus.
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
                                        signOut(() => (isActive.value = false))
                                    "
                                    >KELUAR</v-btn
                                >
                            </v-col>
                        </v-row>
                    </template>
                </form-confirm>
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

            <v-card
                class="mt-9 pt-9"
                elevation="1"
                min-height="calc(100vh - 172px)"
                rounded="lg"
                flat
            >
                <v-card-text class="text-center">
                    <div class="text-body-2 font-weight-medium text-uppercase">
                        {{ auth.username }}
                    </div>
                    <div class="text-caption">{{ auth.usermail }}</div>
                </v-card-text>

                <slot></slot>
            </v-card>
        </v-sheet>
    </v-responsive>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "user-home",

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
        "click:notification": null,
    },

    setup(props) {
        const store = usePageStore();

        store.pageName = props.pageName;
        store.pageKey = props.pageKey;

        const { auth, highlight, modules, navigationState, railMode, theme } =
            storeToRefs(store);

        const { signOut } = store;

        return {
            auth,
            highlight,
            modules,
            navigationState,
            railMode,
            theme,

            signOut,
        };
    },
};
</script>
