<template>
    <div>
        <Head title="Download Certificate" />

        <div class="fixed top-4 right-4 no-print">
            <button 
                @click="printCertificate"
                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-lg"
            >
                Print / Save as PDF
            </button>
        </div>

        <!-- Certificate -->
        <div class="w-full h-screen flex items-center justify-center bg-gray-100 p-8">
            <div id="certificate" class="relative" style="width: 1200px; height: 763px;">
                <!-- Background Certificate Template -->
                <img 
                    src="/images/certificate-blank.png?v=15" 
                    alt="Certificate Template"
                    class="w-full h-full object-contain"
                />
                
                <!-- Overlay Text - Positioned to match the template -->
                <div class="absolute inset-0">
                    
                    <!-- Student Name - Centered under "This certificate is presented to" -->
                    <div class="absolute text-center" style="top: 50%; left: 50%; transform: translate(-50%, -50%); width: 55%;">
                        <p class="text-6xl font-script" style="color: #6B8DC4;">
                            {{ certificate.student_name }}
                        </p>
                    </div>

                    <!-- Course Title - Already positioned correctly -->
                    <div class="absolute text-center" style="top: 65%; left: 50%; transform: translate(-50%, -50%); width: 75%;">
                        <p class="text-2xl font-script" style="color: #2C3E50; letter-spacing: 0.05em;">
                            {{ certificate.course_title }}
                        </p>
                    </div>

                    <!-- Instructor Name - Left side, correct position -->
                    <div class="absolute instructor-name" style="top: 80%; left: 23%; transform: translateY(-50%);">
                        <p class="text-2xl font-script" style="color: #2C3E50;">
                            {{ certificate.instructor_name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    certificate: {
        type: Object,
        required: true
    }
});

const printCertificate = () => {
    window.print();
};

onMounted(() => {
    // Optional: Auto-trigger print dialog after a short delay
    // setTimeout(() => {
    //     window.print();
    // }, 500);
});
</script>

<style scoped>
@font-face {
    font-family: 'Segoe Script';
    src: url('/fonts/SegoeScript.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

.font-script {
    font-family: 'Segoe Script', 'Brush Script MT', 'Apple Chancery', cursive !important;
}

.font-serif {
    font-family: 'Times New Roman', serif;
}

.font-sans {
    font-family: 'Open Sans', sans-serif;
}

@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
    
    #certificate {
        width: 100%;
        height: 100vh;
        box-shadow: none !important;
    }
    
    .instructor-name {
        top: 68% !important;
    }
}
</style>
