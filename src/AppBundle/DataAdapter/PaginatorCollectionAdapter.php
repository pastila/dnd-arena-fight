<?php

namespace AppBundle\DataAdapter;

use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface;
use Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelTransformer;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginatorCollectionAdapter implements ClientApplicationModelAdapterInterface
{
  protected $clientApplicationModelTransformer;

  public function __construct (ClientApplicationModelTransformer $clientApplicationModelTransformer)
  {
    $this->clientApplicationModelTransformer = $clientApplicationModelTransformer;
  }

  /**
   * @param Paginator $subject
   * @param array $options
   * @return array
   */
  public function transform ($subject, $options = array())
  {
    $resolver = new OptionsResolver();
    $resolver->setRequired('adapter');
    $resolver->setDefault('collection_name', 'items');
    $resolver->setDefault('inner_options', []);
    $resolver->setAllowedTypes('adapter', ['Accurateweb\ClientApplicationBundle\DataAdapter\ClientApplicationModelAdapterInterface', 'string']);
    $options = $resolver->resolve($options);
    $collectionAdapter = $options['adapter'];
    $collectionName = $options['collection_name'];

    if ($collectionAdapter instanceof ClientApplicationModelAdapterInterface)
    {
      $collection = [];

      foreach ($subject->getIterator() as $item)
      {
        $collection[] = $collectionAdapter->transform($item, $options['inner_options']);
      }
    }
    else
    {
      $collection = $this->clientApplicationModelTransformer->getClientModelCollectionData($subject->getIterator(), $collectionAdapter);
    }

    $maxPerPage = $subject->getQuery()->getMaxResults();
    $offset = $subject->getQuery()->getFirstResult();
    $count = $subject->count();

    $data = [
      'pager' => [
        'page' => ceil($offset /$maxPerPage) + 1,
        'maxPerPage' => $maxPerPage,
        'firstPage' => 1,
        'lastPage' => ceil($count / $maxPerPage),
        'nbResults' => $count,
      ],
      $collectionName => $collection,
    ];

    return $data;
  }

  public function getModelName ()
  {
    return 'Pages';
  }

  public function supports ($subject)
  {
    return $subject instanceof Paginator;
  }

  public function getName ()
  {
    return 'pagination';
  }
}