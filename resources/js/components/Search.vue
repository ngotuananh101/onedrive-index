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
                    class="justify-between hover:text-neutral-950"
                    @click="rowClick(f)"
                >
                    <span>
                        <p class="text-sm">{{ f.name }}</p>
                        <p class="text-xs text-gray-500">{{ f.path }}</p>
                    </span>
                    <span>{{ f.size }}</span>
                </CommandItem>
            </CommandGroup>
            <CommandSeparator />
            <CommandGroup :heading="this.$t('file')">
                <CommandItem
                    v-for="f in data_file"
                    :key="f.id"
                    :value="f"
                    class="justify-between"
                >
                    <span>
                        <p class="text-sm">{{ f.name }}</p>
                        <p class="text-xs text-gray-500">{{ f.path }}</p>
                    </span>
                    <span>{{ f.size }}</span>
                </CommandItem>
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
            if (!open) {
                this.searchTerm = "";
                this.data_folder = [];
                this.data_file = [];
                clearInterval(this.searchInterval);
            }
            this.$emit("update:open", open);
        },
        rowClick(row) {
            if(row && row.type === "folder"){
                this.$root.need_reload_breadcrumb = true;
                this.$router.push({
                    name: "folders.show",
                    params: { id: row.id },
                });
            }
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
