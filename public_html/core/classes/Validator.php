<?php
/**
 * Sistema de Validação de Dados
 * Valida e sanitiza dados de entrada de forma robusta
 * NOTA: Esta é uma classe ADICIONAL que não altera o código existente
 */
class Validator
{
    private $data = [];
    private $errors = [];
    private $rules = [];
    
    public function __construct($data = [])
    {
        $this->data = $data;
    }
    
    /**
     * Define regras de validação
     */
    public function rules($rules)
    {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Executa a validação
     */
    public function validate()
    {
        $this->errors = [];
        
        foreach ($this->rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $this->validateField($field, $value, $fieldRules);
        }
        
        return empty($this->errors);
    }
    
    /**
     * Valida um campo específico
     */
    private function validateField($field, $value, $rules)
    {
        foreach ($rules as $rule) {
            $ruleName = $rule;
            $ruleValue = true;
            
            // Se a regra tem parâmetros (ex: min:5)
            if (strpos($rule, ':') !== false) {
                list($ruleName, $ruleValue) = explode(':', $rule, 2);
            }
            
            $this->applyRule($field, $value, $ruleName, $ruleValue);
        }
    }
    
    /**
     * Aplica uma regra específica
     */
    private function applyRule($field, $value, $rule, $ruleValue)
    {
        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'O campo é obrigatório');
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Deve ser um email válido');
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleValue) {
                    $this->addError($field, "Deve ter pelo menos {$ruleValue} caracteres");
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleValue) {
                    $this->addError($field, "Deve ter no máximo {$ruleValue} caracteres");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, 'Deve ser um número');
                }
                break;
                
            case 'cpf':
                if (!empty($value) && !$this->validateCPF($value)) {
                    $this->addError($field, 'Deve ser um CPF válido');
                }
                break;
        }
    }
    
    /**
     * Adiciona um erro
     */
    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Obtém todos os erros
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Verifica se há erros
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
    
    /**
     * Obtém dados validados e sanitizados
     */
    public function getValidatedData()
    {
        $validatedData = [];
        
        foreach ($this->rules as $field => $rules) {
            if (isset($this->data[$field])) {
                $validatedData[$field] = htmlspecialchars(trim($this->data[$field]), ENT_QUOTES, 'UTF-8');
            }
        }
        
        return $validatedData;
    }
    
    /**
     * Valida CPF
     */
    private function validateCPF($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Método estático para validação rápida
     */
    public static function make($data, $rules)
    {
        $validator = new self($data);
        $validator->rules($rules);
        
        return $validator;
    }
}
