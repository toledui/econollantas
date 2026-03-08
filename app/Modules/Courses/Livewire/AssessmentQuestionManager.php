<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Assessment;
use App\Modules\Courses\Models\AssessmentQuestion;
use App\Modules\Courses\Models\AssessmentOption;

class AssessmentQuestionManager extends Component
{
    public $assessment;

    public bool $isModal = false;


    public $showQuestionModal = false;
    public $editingQuestionId = null;

    public $questionType = 'single_choice';
    public $questionText = '';
    public $questionPoints = 1;

    public $options = [];

    public function mount($assessment, bool $isModal = false)
    {
        $this->assessment = $assessment instanceof Assessment ? $assessment : Assessment::findOrFail($assessment);
        $this->isModal = $isModal;
    }

    public function openQuestionModal()
    {
        $this->resetQuestionForm();
        $this->addOption(); // Añadir primera opción vacía
        $this->addOption(); // Segunda opción vacía
        $this->showQuestionModal = true;
    }

    public function editQuestion($id)
    {
        $this->resetQuestionForm();
        $question = AssessmentQuestion::with('options')->findOrFail($id);

        $this->editingQuestionId = $question->id;
        $this->questionType = $question->type;
        $this->questionText = $question->question_text;
        $this->questionPoints = rtrim(rtrim(number_format($question->points, 2, '.', ''), '0'), '.');

        if ($question->options->count() > 0) {
            foreach ($question->options as $opt) {
                $this->options[] = [
                    'id' => $opt->id,
                    'text' => $opt->option_text,
                    'is_correct' => $opt->is_correct
                ];
            }
        } else {
            $this->addOption();
            $this->addOption();
        }

        $this->showQuestionModal = true;
    }

    public function addOption()
    {
        $this->options[] = [
            'id' => null,
            'text' => '',
            'is_correct' => false
        ];
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    public function setCorrectOption($index)
    {
        foreach ($this->options as $k => $valid) {
            $this->options[$k]['is_correct'] = false;
        }
        $this->options[$index]['is_correct'] = true;
    }

    public function saveQuestion()
    {
        $this->validate([
            'questionText' => 'required|string',
            'questionPoints' => 'required|numeric|min:0',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
        ], [
            'options.min' => 'Debes proporcionar al menos 2 opciones.',
            'options.*.text.required' => 'El texto de la opción no puede estar vacío.'
        ]);

        // Validate at least one correct option
        $hasCorrect = collect($this->options)->where('is_correct', true)->count() > 0;
        if (!$hasCorrect) {
            $this->addError('options_error', 'Debes seleccionar al menos una respuesta correcta.');
            return;
        }

        if ($this->editingQuestionId) {
            $question = AssessmentQuestion::findOrFail($this->editingQuestionId);
            $question->update([
                'type' => $this->questionType,
                'question_text' => $this->questionText,
                'points' => $this->questionPoints,
            ]);

            // Sync options (delete all old and recreate, or update existing)
            $existingIds = collect($this->options)->pluck('id')->filter()->toArray();
            AssessmentOption::where('question_id', $question->id)->whereNotIn('id', $existingIds)->delete();

            $order = 1;
            foreach ($this->options as $opt) {
                if ($opt['id']) {
                    AssessmentOption::where('id', $opt['id'])->update([
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                        'order' => $order++
                    ]);
                } else {
                    AssessmentOption::create([
                        'question_id' => $question->id,
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                        'order' => $order++
                    ]);
                }
            }

        } else {
            $order = AssessmentQuestion::where('assessment_id', $this->assessment->id)->max('order') + 1;

            $question = AssessmentQuestion::create([
                'assessment_id' => $this->assessment->id,
                'type' => $this->questionType,
                'question_text' => $this->questionText,
                'points' => $this->questionPoints,
                'order' => $order
            ]);

            $optOrder = 1;
            foreach ($this->options as $opt) {
                AssessmentOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                    'order' => $optOrder++
                ]);
            }
        }

        $this->showQuestionModal = false;
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pregunta guardada exitosamente']);
    }

    public function deleteQuestion($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        $question->options()->delete();
        $question->delete();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Pregunta eliminada']);
    }

    public function moveUp($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        /** @var \App\Modules\Courses\Models\AssessmentQuestion|null $previous */
        $previous = AssessmentQuestion::where('assessment_id', $this->assessment->id)
            ->where('order', '<', $question->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($previous) {
            $temp = $question->order;
            $question->update(['order' => $previous->order]);
            $previous->update(['order' => $temp]);
        }
    }

    public function moveDown($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        /** @var \App\Modules\Courses\Models\AssessmentQuestion|null $next */
        $next = AssessmentQuestion::where('assessment_id', $this->assessment->id)
            ->where('order', '>', $question->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $temp = $question->order;
            $question->update(['order' => $next->order]);
            $next->update(['order' => $temp]);
        }
    }

    public function resetQuestionForm()
    {
        $this->editingQuestionId = null;
        $this->questionType = 'single_choice';
        $this->questionText = '';
        $this->questionPoints = 1;
        $this->options = [];
        $this->resetValidation();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.courses.assessment-questions', [
            'questions' => $this->assessment->questions()->orderBy('order')->with('options')->get()
        ]);
    }
}
