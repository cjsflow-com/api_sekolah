<?php

namespace App\Http\Requests\Grades;

use App\Models\TeachingAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',

                Rule::unique(
                    'grades',
                    'student_id'
                )->where(
                    fn ($query) => $query
                        ->where(
                            'teaching_assignment_id',
                            $this->integer(
                                'teaching_assignment_id'
                            )
                        )
                        ->where(
                            'grade_component_id',
                            $this->integer(
                                'grade_component_id'
                            )
                        )
                ),
            ],

            'teaching_assignment_id' => [
                'required',
                'integer',
                'exists:teaching_assignments,id',
            ],

            'grade_component_id' => [
                'required',
                'integer',
                'exists:grade_components,id',
            ],

            'score' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'recorded_by' => [
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
                $assignment = TeachingAssignment::find(
                    $this->integer('teaching_assignment_id')
                );

                if (! $assignment) {
                    return;
                }

                $hasComponent = $assignment
                    ->gradeComponents()
                    ->whereKey(
                        $this->integer('grade_component_id')
                    )
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
            'student_id.required' =>
                'Siswa wajib dipilih.',

            'student_id.exists' =>
                'Siswa tidak ditemukan.',

            'student_id.unique' =>
                'Nilai siswa untuk komponen tersebut sudah tersedia.',

            'teaching_assignment_id.required' =>
                'Penugasan mengajar wajib dipilih.',

            'teaching_assignment_id.exists' =>
                'Penugasan mengajar tidak ditemukan.',

            'grade_component_id.required' =>
                'Komponen nilai wajib dipilih.',

            'grade_component_id.exists' =>
                'Komponen nilai tidak ditemukan.',

            'score.required' =>
                'Nilai wajib diisi.',

            'score.numeric' =>
                'Nilai harus berupa angka.',

            'score.min' =>
                'Nilai minimal 0.',

            'score.max' =>
                'Nilai maksimal 100.',

            'recorded_by.required' =>
                'Guru pencatat wajib ditentukan.',

            'recorded_by.exists' =>
                'Guru pencatat tidak ditemukan.',
        ];
    }
}