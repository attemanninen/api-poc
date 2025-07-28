<?php

namespace App\Form\DataMapper;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

class CriteriaDataMapper implements DataMapperInterface
{
    /**
     * @todo put these in a separate class, "OperatorMap"?
     *
     * @var array
     */
    private const OPERATOR_MAP = [
        // Equals
        'eq' => Comparison::EQ,
        // Not equals
        'ne' => Comparison::NEQ,
        // Greater than
        'gt' => Comparison::GT,
        // Greater or equals
        'ge' => Comparison::GTE,
        // Less than
        'lt' => Comparison::LT,
        // Less or equals
        'le' => Comparison::LTE,
        // In
        'in' => Comparison::IN,
        // Like (%like%)
        'like' => Comparison::CONTAINS,
    ];

    /**
     * @var string
     */
    private $modelClass;

    /**
     * @var array
     */
    private $modelPublicProperties;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
        $this->modelPublicProperties = [];
    }

    /**
     * {@inheritDoc}
     */
    public function mapDataToForms($viewData, iterable $forms)
    {
        if (null === $viewData) {
            return;
        }

        if (!$viewData instanceof Criteria) {
            throw new UnexpectedTypeException($viewData, Criteria::class);
        }

        // Not needed for now.
    }

    /**
     * {@inheritDoc}
     */
    public function mapFormsToData(iterable $forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        $viewData->setMaxResults($forms['pageSize']->getData());
        $viewData->setFirstResult($forms['page']->getData());

        if ($orderBys = $forms['orderBy']->getData()) {
            $orderings = [];

            foreach (explode(',', $orderBys) as $orderBy) {
                [$field, $order] = explode(' ', $orderBy);

                if (!in_array($field, $this->getModelPublicProperties())) {
                    continue;
                }

                $order = strtoupper($order) === 'ASC' ? Criteria::ASC : Criteria::DESC;
                $orderings[$field] = $order;
            }
            $viewData->orderBy($orderings);
        }

        if ($filters = $forms['filters']->getData()) {
            foreach (explode(',', $filters) as $filter) {
                [$field, $operator, $values] = explode(':', $filter);

                if (!in_array($field, $this->getModelPublicProperties())) {
                    continue;
                }

                foreach (explode('|', $values) as $index => $value) {
                    $expr = new Comparison($field, self::OPERATOR_MAP[$operator], $value);

                    if ($index === 0) {
                        $viewData->andWhere($expr);
                    } else {
                        $viewData->orWhere($expr);
                    }
                }
            }
        }

        if ($search = $forms['search']->getData()) {
            $properties = $this->getModelPublicProperties();

            foreach ($properties as $index => $property) {
                $expr = new Comparison($property, Comparison::CONTAINS, $search);

                if ($index === 0) {
                    $viewData->andWhere($expr);
                } else {
                    $viewData->orWhere($expr);
                }
            }
        }
    }

    private function getModelPublicProperties(): array
    {
        if (!$this->modelPublicProperties) {
            $serializerClassMetadataFactory = new ClassMetadataFactory(
                new AnnotationLoader(new AnnotationReader())
            );
            $serializerExtractor = new SerializerExtractor($serializerClassMetadataFactory);
            $this->modelPublicProperties = $serializerExtractor->getProperties(
                $this->modelClass,
                ['serializer_groups' => ['public']]
            );
        }

        return $this->modelPublicProperties;
    }
}
