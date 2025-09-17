<script setup lang="ts">
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps<{
  title: string
  message: string
}>()

const emit = defineEmits<{
  (e: 'close'): void
}>()

const isOpen = ref(true)
</script>

<template>
<Dialog v-model:open="isOpen" @update:open="emit('close')">
  <DialogContent
    class="w-full max-w-sm border border-sky-300
           bg-gradient-to-br from-sky-100 via-white to-sky-50/80
           backdrop-blur-md rounded-2xl shadow-2xl p-6
           transition-all duration-300 ease-out
           opacity-0 scale-95 data-[state=open]:opacity-100 data-[state=open]:scale-100"
  >
    <!-- Header terpusat -->
    <DialogHeader class="flex flex-col items-center text-center gap-2">
      <DialogTitle class="text-sky-900 text-xl font-bold hover:text-sky-700 transition">
        {{ props.title }}
      </DialogTitle>
      <DialogDescription class="text-sky-800 text-sm leading-relaxed">
        <p v-html=" props.message" class="text-sm text-red-600 whitespace-pre-line" />
      </DialogDescription>
    </DialogHeader>

    <!-- Tombol footer terpusat -->
    <DialogFooter class="mt-6 w-full flex justify-center">
      <Button
        class="px-5 py-2 bg-gradient-to-r from-red-500 to-red-600
               hover:from-red-600 hover:to-red-700 text-white
               font-semibold rounded-lg shadow-md
               transition-transform transform hover:scale-105 active:scale-95 cursor-pointer"
        @click="emit('close')"
      >
        Tutup
      </Button>
    </DialogFooter>
  </DialogContent>
</Dialog>

</template>
