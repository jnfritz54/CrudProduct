<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class ProductController extends AbstractController
{
    /*
     * Index & Product list
     */
    #[Route('/', name: 'ctrl_product_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $form = $this->createForm(ProductSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            /* Note: if product references have to follow the same pattern,
             we can analyse the search data here before searching only in references if a valid reference format is provided by the user.
            (In that case we'll also have to validate that format when creating/editing a product) */
            $products = $productRepository->searchOnNameAndReference($data['search']);
        } else {
            $products = $productRepository->findAll();
        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /*
     * method called by twig templates to render search form
     */
    #[Route('/search', name: 'ctrl_product_search', methods: ['GET'])]
    public function search(): Response
    {
        $form = $this->createForm(ProductSearchType::class);
        return $this->render('product/_part_search.html.twig', ['form' => $form->createView()]);
    }

    /*
     * Create page
     */
    #[Route('/new', name: 'ctrl_product_new', methods: ['GET', 'POST'])]
    public function newProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('ctrl_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /*
     * detailed product view page
     */
    #[Route('/{id}/show', name: 'ctrl_product_show', requirements: ["id" => "\d+"], methods: ['GET'])]
    public function showProduct(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /*
     * edit product page
     */
    #[Route('/{id}/edit', name: 'ctrl_product_edit', requirements: ["id" => "\d+"], methods: ['GET', 'POST'])]
    public function editProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('ctrl_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /*
     * delete a product permanently
     */
    #[Route('/{id}/delete', name: 'ctrl_product_delete', requirements: ["id" => "\d+"], methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), (string)$request->request->get('_token'))) {
            $productRepository->remove($product, true);
            $this->addFlash('notice', "Le produit a bien été supprimé");
        } else {
            $this->addFlash(
                'error',
                "Erreur: Le produit n'a pu être supprimé. (Impossible de procéder à la validation CSRF) Merci de réessayer après avoir rafraîchit la page"
            );
        }

        return $this->redirectToRoute('ctrl_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
