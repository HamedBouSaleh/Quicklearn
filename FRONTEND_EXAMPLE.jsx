// Example React/Vue component for AI Quiz Generation
// This can be integrated into your Inertia.js pages

// Example usage in Instructor/Lessons/Show.jsx or similar

import { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function GenerateQuizModal({ lessonId, show, onClose }) {
    const { data, setData, post, processing, errors } = useForm({
        number_of_questions: 5,
        question_types: ['mcq', 'true_false'],
        quiz_title: '',
        quiz_description: '',
        time_limit: 600,
        passing_score: 70,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('instructor.quizzes.generate', lessonId), {
            onSuccess: () => {
                onClose();
            },
        });
    };

    if (!show) return null;

    return (
        <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div className="mt-3">
                    <h3 className="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Generate Quiz with AI
                    </h3>
                    
                    <form onSubmit={handleSubmit} className="space-y-4">
                        {/* Number of Questions */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Number of Questions
                            </label>
                            <input
                                type="number"
                                min="1"
                                max="20"
                                value={data.number_of_questions}
                                onChange={(e) => setData('number_of_questions', parseInt(e.target.value))}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            {errors.number_of_questions && (
                                <p className="mt-1 text-sm text-red-600">{errors.number_of_questions}</p>
                            )}
                        </div>

                        {/* Question Types */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Question Types
                            </label>
                            <div className="space-y-2">
                                <label className="inline-flex items-center mr-4">
                                    <input
                                        type="checkbox"
                                        checked={data.question_types.includes('mcq')}
                                        onChange={(e) => {
                                            const types = e.target.checked
                                                ? [...data.question_types, 'mcq']
                                                : data.question_types.filter(t => t !== 'mcq');
                                            setData('question_types', types);
                                        }}
                                        className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span className="ml-2 text-sm text-gray-700">Multiple Choice</span>
                                </label>
                                <label className="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        checked={data.question_types.includes('true_false')}
                                        onChange={(e) => {
                                            const types = e.target.checked
                                                ? [...data.question_types, 'true_false']
                                                : data.question_types.filter(t => t !== 'true_false');
                                            setData('question_types', types);
                                        }}
                                        className="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span className="ml-2 text-sm text-gray-700">True/False</span>
                                </label>
                            </div>
                        </div>

                        {/* Quiz Title (Optional) */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Quiz Title (Optional)
                            </label>
                            <input
                                type="text"
                                value={data.quiz_title}
                                onChange={(e) => setData('quiz_title', e.target.value)}
                                placeholder="Leave empty for auto-generated title"
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Time Limit */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Time Limit (minutes)
                            </label>
                            <input
                                type="number"
                                min="1"
                                value={data.time_limit / 60}
                                onChange={(e) => setData('time_limit', parseInt(e.target.value) * 60)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Passing Score */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                Passing Score (%)
                            </label>
                            <input
                                type="number"
                                min="0"
                                max="100"
                                value={data.passing_score}
                                onChange={(e) => setData('passing_score', parseInt(e.target.value))}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Buttons */}
                        <div className="flex items-center justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                                disabled={processing}
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={processing || data.question_types.length === 0}
                                className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                            >
                                {processing ? 'Generating...' : 'Generate Quiz'}
                            </button>
                        </div>
                    </form>

                    {/* Info Box */}
                    <div className="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p className="text-xs text-blue-800">
                            <strong>Note:</strong> AI will analyze your lesson content to generate relevant questions. 
                            You can review and edit them before publishing.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Usage example in a page:
/*
import GenerateQuizModal from './GenerateQuizModal';

function LessonShow({ lesson }) {
    const [showModal, setShowModal] = useState(false);

    return (
        <div>
            <button 
                onClick={() => setShowModal(true)}
                className="px-4 py-2 bg-purple-600 text-white rounded-md"
            >
                🤖 Generate Quiz with AI
            </button>

            <GenerateQuizModal
                lessonId={lesson.id}
                show={showModal}
                onClose={() => setShowModal(false)}
            />
        </div>
    );
}
*/
