<template lang="">
    <CommandDialog :open="open" :searchTerm="searchTerm" @update:open="handleOpenChange" @update:searchTerm="updateSearch">
        <CommandInput :placeholder="this.$t('search_placeholder')" />
        <CommandList>
            <CommandEmpty>{{ this.$t('no_result') }}</CommandEmpty>
            <CommandGroup heading="Suggestions">
                <CommandItem value="calendar"> Calendar </CommandItem>
                <CommandItem value="search-emoji"> Search Emoji </CommandItem>
                <CommandItem value="calculator"> Calculator </CommandItem>
            </CommandGroup>
            <CommandSeparator />
            <CommandGroup heading="Settings">
                <CommandItem value="profile"> Profile </CommandItem>
                <CommandItem value="billing"> Billing </CommandItem>
                <CommandItem value="settings"> Settings </CommandItem>
            </CommandGroup>
        </CommandList>
    </CommandDialog>
</template>
<script>
import {
    CommandDialog,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
} from "@/components/ui/command";
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
            value: [],
            searchTerm: "123",
        };
    },
    methods: {
        handleOpenChange(open) {
            this.$emit("update:open", open);
        },
        updateSearch(value){
            console.log(value);
        }
    },
};
</script>
<style lang=""></style>
