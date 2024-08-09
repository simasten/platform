<template>
    <v-sheet>
        <slot></slot>

        <v-dialog
            v-model="dialogFile"
            transition="dialog-bottom-transition"
            fullscreen
        >
            <v-sheet height="100vh">
                <v-toolbar :color="theme">
                    <v-btn icon @click="dialogFile = false">
                        <v-icon>close</v-icon>
                    </v-btn>

                    <v-toolbar-title class="text-overline"
                        >document</v-toolbar-title
                    >

                    <v-btn size="small" icon @click="$refs.pdf.zoomIn()">
                        <v-icon size="x-large">add_circle</v-icon>
                    </v-btn>

                    <v-btn size="small" icon @click="$refs.pdf.zoomOut()">
                        <v-icon size="x-large">remove_circle</v-icon>
                    </v-btn>
                </v-toolbar>

                <view-pdf
                    ref="pdf"
                    :datapath="datapath"
                    :value="currentFile"
                ></view-pdf>
            </v-sheet>
        </v-dialog>
    </v-sheet>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "file-list",

    props: {
        datapath: String,
    },

    setup() {
        const store = usePageStore();

        const { currentFile, dialogFile, theme } = storeToRefs(store);

        return {
            currentFile,
            dialogFile,
            theme,
        };
    },
};
</script>
