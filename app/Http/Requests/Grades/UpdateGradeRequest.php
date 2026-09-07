<?php

namespace App\Http\Requests\Grades;

use App\Models\TeachingAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $grade = $this->route('grade');

        $assignmentId = $this->integer(
            'teaching_assignment_id',
            $grade->teaching_assignment_id
        );

        $componentId = $this->integer(
            'grade_component_id',
            $grade->grade_component_id
        );

        return [
            'student_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:students,id',

                Rule::unique(
                    'grades',
                    'student_id'
                )
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'teaching_assignment_id',
                                $assignmentId
                            )
                            ->where(
                                'grade_component_id',
                                $componentId
                            )
                    )
                    ->ignore($grade),
            ],

            'teaching_assignment_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:teaching_assignments,id',
            ],

            'grade_component_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:grade_components,id',
            ],

            'score' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'recorded_by' => [
                'sometimes',
                'required',
                'integer',
                'exists:teachers,id',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $grade = $this->route('grade');

                $assignmentId = $this->integer(
                    'teaching_assignment_id',
                    $grade->teaching_assignment_id
                );

                $componentId = $this->integer(
                    'grade_component_id',
                    $grade->grade_component_id
                );

                $assignment = TeachingAssignment::find(
                    $assignmentId
                );

                if (! $assignment) {
                    return;
                }

                $hasComponent = $assignment
                    ->gradeComponents()
                    ->whereKey($componentId)
                    ->exists();

                if (! $hasComponent) {
                    $validator->errors()->add(
                        'grade_component_id',
                        'Komponen nilai tidak terdaftar pada penugasan mengajar tersebut.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.exists' =>
                'Siswa tidak ditemukan.',

            'student_id.unique' =>
                'Nilai siswa untuk komponen tersebut sudah tersedia.',

            'teaching_assignment_id.exists' =>
                'Penugasan mengajar tidak ditemukan.',

            'grade_component_id.exists' =>
                'Komponen nilai tidak ditemukan.',

            'score.numeric' =>
                'Nilai harus berupa angka.',

            'score.min' =>
                'Nilai minimal 0.',

            'score.max' =>
                'Nilai maksimal 100.',

            'recorded_by.exists' =>
                'Guru pencatat tidak ditemukan.',
        ];
    }
}