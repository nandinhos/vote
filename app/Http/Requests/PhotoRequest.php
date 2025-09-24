<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PhotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return Auth::check() && $user && $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'project_id' => 'required|exists:projects,id',
            'photo' => $isUpdate ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:51200' : 'required_without:photos|image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'photos' => 'nullable|array|max:20',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:51200',
            'caption' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'O projeto é obrigatório.',
            'project_id.exists' => 'O projeto selecionado não existe.',
            'photo.required_without' => 'Pelo menos uma foto é obrigatória.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A foto deve ser do tipo: jpeg, png, jpg, gif ou webp.',
            'photo.max' => 'A foto não pode ser maior que 50MB.',
            'photos.array' => 'As fotos devem ser enviadas como um array.',
            'photos.max' => 'Você pode enviar no máximo 20 fotos por vez.',
            'photos.*.image' => 'Todos os arquivos devem ser imagens.',
            'photos.*.mimes' => 'Todas as fotos devem ser do tipo: jpeg, png, jpg, gif ou webp.',
            'photos.*.max' => 'Cada foto não pode ser maior que 50MB.',
            'caption.max' => 'A legenda não pode ter mais de 255 caracteres.',
        ];
    }
}
