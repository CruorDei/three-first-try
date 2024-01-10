<?php 

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\UnderProduct;
use App\Form\ProductFormType;
use App\Form\UnderProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product', name: 'app_admin_product_')]
class saveProductController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $em): Response
    {
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findAll();

        return $this->render('admin/product/index.html.twig', [
            'controller_name' => 'UserController',
            'products' => $products
        ]);
    }

    #[Route('/addUnderProduct/{id}/', name: 'addUnderProduct')]
    public function addUnderProduct(Request $request, Product $product, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');

        $underProduct = new UnderProduct();
        $underProduct->setParentProduct($product);

        $underProductForm = $this->createForm(UnderProductFormType::class, $underProduct);

        $underProductForm->handleRequest($request);

        if ($underProductForm->isSubmitted() && $underProductForm->isValid()) {
            $slug = $slugger->slug($underProduct->getNum())->lower();
            $underProduct->setSlug($slug);
            $product = $underProduct->getParentProduct();
            $product->newModifiedAt();
            $em->persist($underProduct);
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'UnderProduct ajouté.');

            return $this->redirectToRoute('app_admin_product_show', ['id' => $product->getId()]);
        }

        return $this->render('admin/product/UnderProduct.html.twig', [
            'underProductForm' => $underProductForm->createView(),
            'product' => $product
        ]);
    }

    #[Route('/{product_id}/delete/{id}/', name: 'deleteUnderProduct')]
    public function deleteUnderProduct(UnderProduct $underproduct, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        // Supprime le sous produit de la base de données
        $product = $underproduct->getParentProduct();
        $em->remove($underproduct);
        $em->flush();
    
        // Redirige l'utilisateur vers la liste des sous produits
        return $this->redirectToRoute('app_admin_product_show', ['id' => $product->getId()]);

    }

    #[Route('/{id}', name: 'show')]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');

        $product = new Product;

        $productForm = $this->createForm(ProductFormType::class, $product);

        //requete
        $productForm->handleRequest($request);

        //verif
        if($productForm->isSubmitted()&&$productForm->isValid()){
            $slug = $slugger->slug($product->getName())->lower();
            $product->newModifiedAt();
            $product->setSlug($slug);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'produit ajouté');

            return $this->redirectToRoute('app_admin_product_index');
        }

        return $this->render('admin/product/add.html.twig', [
            'controller_name' => 'UserController',
            'productForm' => $productForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_PRODUCT_ADMIN');

        $productForm = $this->createForm(ProductFormType::class, $product);

        //requete
        $productForm->handleRequest($request);

        //verif
        if($productForm->isSubmitted()&&$productForm->isValid()){
            $slug = $slugger->slug($product->getName())->lower();
            $product->newModifiedAt();
            $product->setSlug($slug);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'produit modifié');

            return $this->redirectToRoute('app_admin_product_index');
        }




        return $this->render('admin/product/edit.html.twig', [
            'controller_name' => 'UserController',
            'productForm' => $productForm->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    // Supprime le produit de la base de données
    $em->remove($product);
    $em->flush();

    // Redirige l'utilisateur vers la liste des produits
    return $this->redirectToRoute('app_admin_product_index');
    }
}

?>