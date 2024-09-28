<template>
    <v-navigation-drawer
        v-model="helpState"
        color="white"
        location="right"
        width="360"
        disable-resize-watcher
        style="height: calc(100dvh)"
    >
        <template v-slot:prepend>
            <v-toolbar color="white" height="72">
                <v-toolbar-title class="text-overline">
                    {{ title }}
                </v-toolbar-title>

                <v-spacer></v-spacer>
            </v-toolbar>

            <v-divider></v-divider>
        </template>

        <template v-slot:default>
            <v-card-text>
                <div class="text-overline">Form</div>
                <v-divider></v-divider>

                <div class="text-caption text-grey-darken-1 pt-2">
                    <slot name="info"
                        >Form ini berfungsi untuk menampilkan form data.</slot
                    >
                </div>

                <slot></slot>

                <slot name="icon">
                    <widget-icon :mode="mode"></widget-icon>
                </slot>

                <widget-activity v-if="withActivityLogs"></widget-activity>
            </v-card-text>
        </template>
    </v-navigation-drawer>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "form-help",

    props: {
        mode: {
            type: String,
            default: "show",
        },

        title: {
            type: String,
            default: "information",
        },

        withActivityLogs: Boolean,
    },

    setup() {
        const store = usePageStore();

        store.helpState = true;

        const { helpState, logs, page, theme } = storeToRefs(store);

        return {
            helpState,
            logs,
            page,
            theme,
        };
    },
};
</script>
