<script setup>
import { computed } from "vue";

const model = defineModel();
const props = defineProps({
  label: String,
  iType: String,
  required: Boolean,
  error: Array,
});

const uid = crypto.getRandomValues(new Uint32Array(1))[0].toString(36);

const type = computed(() => {
  return props.iType ?? "text";
});
</script>

<template>
  <div>
    <label :for="uid" class="block text-sm font-medium leading-6 text-gray-900">
      {{ props.label }}
    </label>
    <div class="mt-2">
      <input
        :required="props.required"
        :type
        :name="uid"
        :id="uid"
        v-model="model"
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
      />
    </div>
  </div>
  {{ error }}
  <!-- s<p v-if="props.error" class="mt-1 text-xs text-red-500">{{ error }}</p> -->
</template>
