<template>
    <StudentLayout>
        <Head title="Certificate" />

        <div class="max-w-5xl mx-auto">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Your Certificate</h1>
                <p class="text-gray-600">Congratulations on completing the course!</p>
            </div>

            <!-- Certificate Preview -->
            <Card class="mb-8 overflow-hidden">
                <div class="bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 p-12 border-8 border-double border-amber-600">
                    <div class="bg-white rounded-lg shadow-2xl p-12 relative overflow-hidden">
                        <!-- Decorative Elements -->
                        <div class="absolute top-0 left-0 w-32 h-32 border-t-4 border-l-4 border-amber-400"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 border-t-4 border-r-4 border-amber-400"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 border-b-4 border-l-4 border-amber-400"></div>
                        <div class="absolute bottom-0 right-0 w-32 h-32 border-b-4 border-r-4 border-amber-400"></div>

                        <div class="relative z-10">
                            <!-- Certificate Badge -->
                            <div class="flex justify-center mb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-amber-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Title -->
                            <div class="text-center mb-8">
                                <h2 class="text-5xl font-serif font-bold text-gray-800 mb-2">Certificate of Completion</h2>
                                <div class="w-24 h-1 bg-amber-500 mx-auto"></div>
                            </div>

                            <!-- Text Content -->
                            <div class="text-center mb-8 space-y-4">
                                <p class="text-xl text-gray-700">This is to certify that</p>
                                <p class="text-4xl font-bold text-gray-900 font-serif">{{ certificate.student_name }}</p>
                                <p class="text-xl text-gray-700">has successfully completed the course</p>
                                <p class="text-3xl font-bold text-blue-600 font-serif">{{ certificate.course_title }}</p>
                            </div>

                            <!-- Course Details -->
                            <div class="grid grid-cols-3 gap-8 mb-8 text-center">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Date Issued</p>
                                    <p class="font-semibold text-gray-900">{{ certificate.issued_date }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Certificate ID</p>
                                    <p class="font-semibold text-gray-900">{{ certificate.certificate_id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Final Score</p>
                                    <p class="font-semibold text-gray-900">{{ certificate.final_score }}%</p>
                                </div>
                            </div>

                            <!-- Signature Section -->
                            <div class="grid grid-cols-2 gap-12 mt-12 pt-8 border-t border-gray-300">
                                <div class="text-center">
                                    <div class="mb-2">
                                        <div class="h-12 flex items-end justify-center">
                                            <span class="text-2xl font-signature text-gray-700">{{ certificate.instructor_name || 'Instructor' }}</span>
                                        </div>
                                    </div>
                                    <div class="border-t-2 border-gray-400 pt-2">
                                        <p class="text-sm font-semibold text-gray-700">Instructor Signature</p>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="mb-2">
                                        <div class="h-12 flex items-center justify-center">
                                            <img 
                                                src="/images/logo.png" 
                                                alt="Learn Logo" 
                                                class="h-10"
                                                onerror="this.style.display='none'"
                                            />
                                            <span class="text-2xl font-bold text-blue-600">Learn</span>
                                        </div>
                                    </div>
                                    <div class="border-t-2 border-gray-400 pt-2">
                                        <p class="text-sm font-semibold text-gray-700">Official Seal</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Verification Link -->
                            <div class="mt-8 text-center">
                                <p class="text-xs text-gray-500">
                                    Verify this certificate at: {{ verificationUrl }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>

            <!-- Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <button 
                    @click="downloadPDF"
                    :disabled="downloading"
                    class="flex items-center justify-center px-6 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ downloading ? 'Generating PDF...' : 'Download PDF' }}
                </button>

                <button 
                    @click="shareCertificate"
                    class="flex items-center justify-center px-6 py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Share
                </button>

                <Link 
                    :href="route('certificates.index')"
                    class="flex items-center justify-center px-6 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-blue-500 hover:text-blue-600 transition"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    All Certificates
                </Link>
            </div>

            <!-- Achievement Stats -->
            <Card>
                <template #header>
                    <h2 class="text-xl font-bold text-gray-900">Course Achievement Summary</h2>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600 mb-2">{{ certificate.lessons_completed }}</div>
                        <div class="text-sm text-gray-600">Lessons Completed</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600 mb-2">{{ certificate.quizzes_passed }}</div>
                        <div class="text-sm text-gray-600">Quizzes Passed</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600 mb-2">{{ certificate.hours_spent }}</div>
                        <div class="text-sm text-gray-600">Hours Spent</div>
                    </div>
                    <div class="text-center p-4 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg">
                        <div class="text-3xl font-bold text-amber-600 mb-2">{{ certificate.final_score }}%</div>
                        <div class="text-sm text-gray-600">Final Score</div>
                    </div>
                </div>
            </Card>

            <!-- Social Share Modal (Hidden by default) -->
            <div v-if="showShareModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showShareModal = false">
                <Card class="max-w-md w-full mx-4" @click.stop>
                    <template #header>
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Share Your Achievement</h3>
                            <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <button class="w-full flex items-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Share on Facebook
                        </button>
                        <button class="w-full flex items-center px-4 py-3 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            Share on Twitter
                        </button>
                        <button class="w-full flex items-center px-4 py-3 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition">
                            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            Share on LinkedIn
                        </button>
                        <button @click="copyLink" class="w-full flex items-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            {{ copied ? 'Link Copied!' : 'Copy Link' }}
                        </button>
                    </div>
                </Card>
            </div>
        </div>
    </StudentLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Layouts/StudentLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    certificate: {
        type: Object,
        required: true
    }
});

const downloading = ref(false);
const showShareModal = ref(false);
const copied = ref(false);

const verificationUrl = computed(() => {
    return `${window.location.origin}/verify/${props.certificate.certificate_id}`;
});

const downloadPDF = () => {
    downloading.value = true;
    router.get(route('certificates.download', props.certificate.id), {}, {
        onFinish: () => {
            downloading.value = false;
        }
    });
};

const shareCertificate = () => {
    showShareModal.value = true;
};

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(verificationUrl.value);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

.font-signature {
    font-family: 'Great Vibes', cursive;
}
</style>
