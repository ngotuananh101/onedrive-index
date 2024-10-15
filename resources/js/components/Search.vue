<template lang="">
    <CommandDialog
        :open="open"
        @update:open="handleOpenChange"
        v-model:searchTerm="searchTerm"
    >
        <CommandInput :placeholder="this.$t('search_placeholder')" />
        <CommandList>
            <CommandEmpty>{{ this.$t("no_result") }}</CommandEmpty>
            <CommandGroup :heading="this.$t('folder')">
                <CommandItem
                    v-for="f in data_folder"
                    :key="f.id"
                    :value="f"
                    class="outline-0"
                >
                    {{ f.name }}</CommandItem
                >
            </CommandGroup>
            <CommandSeparator />
            <CommandGroup :heading="this.$t('file')">
                <CommandItem
                    v-for="f in data_file"
                    :key="f.id"
                    :value="f"
                    class="outline-0"
                >
                    {{ f.name }}</CommandItem
                >
            </CommandGroup>
        </CommandList>
    </CommandDialog>
</template>
<script>
import {
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
} from "@/components/ui/command";
import CommandDialog from "@/components/CommandDialog.vue";
import { searchFileAndFolder } from "../services/driveService";
export default {
    components: {
        CommandDialog,
        CommandEmpty,
        CommandGroup,
        CommandInput,
        CommandItem,
        CommandList,
        CommandSeparator,
    },
    props: {
        open: Boolean,
    },
    data() {
        return {
            searchTerm: "",
            data_folder: [],
            data_file: [],
            searchInterval: null,
        };
    },
    methods: {
        handleOpenChange(open) {
            console.log(open);

            this.$emit("update:open", open);
        },
    },
    watch: {
        searchTerm(value) {
            if (value) {
                clearInterval(this.searchInterval);
                this.searchInterval = setTimeout(() => {
                    searchFileAndFolder({ query: value }).then((res) => {
                        this.data_folder = res.data_folder ?? [];
                        this.data_file = res.data_file ?? [];
                    });
                }, 500);
            }
        },
    },
};
</script>
<style lang=""></style>
