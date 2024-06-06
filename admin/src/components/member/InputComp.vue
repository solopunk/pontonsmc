<script setup>
import { computed } from "vue";

const model = defineModel();

const props = defineProps({
  label: String,
  xType: String,
  col: String,
  required: Boolean,
});

const uid = crypto.getRandomValues(new Uint32Array(1))[0].toString(36);

const type = computed(() => {
  return props.xType ?? "text";
});

const col = computed(() => {
  let colSpan = undefined;
  if (props.col === "full") {
    colSpan = "col-span-full";
  } else {
    colSpan = props.col ? `sm:col-span-${props.col}` : "sm:col-span-3";
  }
  return colSpan;
});
</script>

<template>
  <div :class="col">
    <label :for="uid" class="block text-sm font-medium leading-6 text-gray-900">
      {{ props.label }}
    </label>
    <div class="mt-2">
      <input
        autocomplete="given-name"
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
        :required="props.required"
        :type
        :name="uid"
        :id="uid"
        v-model="model"
        :step="type === 'number' ? '0.01' : null"
      />
    </div>
  </div>
</template>
