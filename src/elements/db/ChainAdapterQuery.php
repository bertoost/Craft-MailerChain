<?php

namespace bertoost\mailerchain\elements\db;

use bertoost\mailerchain\elements\ChainAdapter;
use craft\db\Connection;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * @method ChainAdapter[]|array all($db = null)
 * @method ChainAdapter|array|null one($db = null)
 * @method ChainAdapter|array|null nth(int $n, ?Connection $db = null)
 */
class ChainAdapterQuery extends ElementQuery
{
    public string $transportType = '';

    public string $transportClass = '';

    protected array $defaultOrderBy = [
        'FIELD(mailerchain.ranking, 0)' => SORT_ASC,
        'mailerchain.ranking' => SORT_ASC,
    ];

    public function transportType(string $value): self
    {
        $this->transportType = $value;

        return $this;
    }

    public function transportClass(string $value): self
    {
        $this->transportClass = $value;

        return $this;
    }

    public function random(): self
    {
        $this->orderBy = null;

        parent::orderBy('RAND()');

        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('mailerchain');

        $this->query->select([
            'mailerchain.transportType',
            'mailerchain.transportSettings',
            'mailerchain.transportClass',
            'mailerchain.sent',
            'mailerchain.ranking',
        ]);

        if ($this->transportType) {
            $this->subQuery->andWhere(Db::parseParam('mailerchain.transportType', $this->transportType));
        }

        if ($this->transportClass) {
            $this->subQuery->andWhere(Db::parseParam('mailerchain.transportClass', $this->transportClass));
        }

        return parent::beforePrepare();
    }
}