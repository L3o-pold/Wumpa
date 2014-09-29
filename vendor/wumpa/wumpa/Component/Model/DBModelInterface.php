<?php

namespace Wumpa\Component\Model;

interface DBModelInterface {

    const EQ =
    const NEQ =
    const BETWEEN =
    const GT =
    const LT =
    const LIKE =
    const NLIKE =
    const IN =
    const NIN =


    public static function getById($id, $colsToGet = null);

    public static function getWhere($col, $op, $val, $colsToGet = null);
    
}



// getById
SELECT *
FROM [TABLE NAME]
WHERE [PrimaryKey] = [DATA]

SELECT [cols1], [cols2], [...]
FROM [TABLE NAME]
WHERE [PrimaryKey] = [DATA]

// getWhere
SELECT *
FROM [TABLE NAME]
WHERE [col] [OP] [VAL]

etc..
