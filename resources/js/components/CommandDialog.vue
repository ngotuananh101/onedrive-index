<script setup>
import {
    Dialog,
    DialogHeader,
    DialogContent,
    DialogTitle,
    DialogDescription,
} from "/resources/js/components/ui/dialog";
import { useForwardPropsEmits } from "radix-vue";
import Command from "./ui/command/Command.vue";

const props = defineProps({
    open: { type: Boolean, required: false },
    defaultOpen: { type: Boolean, required: false },
    modal: { type: Boolean, required: false },
    searchTerm: { type: String, required: false },
    filterFunction: { type: Function, required: false },
    displayValue: { type: Function, required: false },
    selectedValue: { type: null, required: false },
});
const emits = defineEmits([
    "update:open",
    "update:searchTerm",
    "update:selectedValue"
]);
const forwarded = useForwardPropsEmits(props, emits);
</script>

<template>
    <Dialog v-bind="forwarded">
        <DialogContent class="p-0 overflow-hidden shadow-lg">
            <DialogHeader class="hidden">
                <DialogTitle></DialogTitle>
                <DialogDescription></DialogDescription>
            </DialogHeader>
            <Command
                class="[&_[cmdk-group-heading]]:px-2 [&_[cmdk-group-heading]]:font-medium [&_[cmdk-group-heading]]:text-muted-foreground [&_[cmdk-group]:not([hidden])_~[cmdk-group]]:pt-0 [&_[cmdk-group]]:px-2 [&_[cmdk-input-wrapper]_svg]:h-5 [&_[cmdk-input-wrapper]_svg]:w-5 [&_[cmdk-input]]:h-12 [&_[cmdk-item]]:px-2 [&_[cmdk-item]]:py-3 [&_[cmdk-item]_svg]:h-5 [&_[cmdk-item]_svg]:w-5"
                v-bind="forwarded"
            >
                <slot />
            </Command>
        </DialogContent>
    </Dialog>
</template>
